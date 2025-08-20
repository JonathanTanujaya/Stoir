import asyncio
import websockets
import json
import logging
from datetime import datetime
from typing import Dict, Set, List
import uuid

# Setup logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class StockWebSocketServer:
    def __init__(self, host='localhost', port=8080):
        self.host = host
        self.port = port
        self.clients: Set[websockets.WebSocketServerProtocol] = set()
        self.subscriptions: Dict[str, Set[websockets.WebSocketServerProtocol]] = {}
        self.stock_data: Dict[str, any] = {}
        
    async def register_client(self, websocket):
        """Register a new client connection"""
        self.clients.add(websocket)
        client_id = str(uuid.uuid4())
        websocket.client_id = client_id
        logger.info(f"Client {client_id} connected. Total clients: {len(self.clients)}")
        
        # Send initial stock data
        await self.send_to_client(websocket, {
            'type': 'INITIAL_DATA',
            'payload': {
                'stockData': list(self.stock_data.values()),
                'timestamp': datetime.now().isoformat()
            }
        })
        
    async def unregister_client(self, websocket):
        """Unregister a client connection"""
        self.clients.discard(websocket)
        
        # Remove from all subscriptions
        for topic, subscribers in self.subscriptions.items():
            subscribers.discard(websocket)
            
        logger.info(f"Client {getattr(websocket, 'client_id', 'unknown')} disconnected. Total clients: {len(self.clients)}")
        
    async def subscribe_to_topic(self, websocket, topic: str):
        """Subscribe a client to a specific topic"""
        if topic not in self.subscriptions:
            self.subscriptions[topic] = set()
        self.subscriptions[topic].add(websocket)
        logger.info(f"Client {getattr(websocket, 'client_id', 'unknown')} subscribed to {topic}")
        
    async def unsubscribe_from_topic(self, websocket, topic: str):
        """Unsubscribe a client from a specific topic"""
        if topic in self.subscriptions:
            self.subscriptions[topic].discard(websocket)
            
    async def send_to_client(self, websocket, message: dict):
        """Send message to a specific client"""
        try:
            await websocket.send(json.dumps(message))
        except websockets.exceptions.ConnectionClosed:
            await self.unregister_client(websocket)
        except Exception as e:
            logger.error(f"Error sending message to client: {e}")
            
    async def broadcast_to_all(self, message: dict):
        """Broadcast message to all connected clients"""
        if not self.clients:
            return
            
        disconnected = set()
        for websocket in self.clients:
            try:
                await websocket.send(json.dumps(message))
            except websockets.exceptions.ConnectionClosed:
                disconnected.add(websocket)
            except Exception as e:
                logger.error(f"Error broadcasting to client: {e}")
                disconnected.add(websocket)
                
        # Clean up disconnected clients
        for websocket in disconnected:
            await self.unregister_client(websocket)
            
    async def broadcast_to_topic(self, topic: str, message: dict):
        """Broadcast message to all clients subscribed to a topic"""
        if topic not in self.subscriptions:
            return
            
        disconnected = set()
        for websocket in self.subscriptions[topic]:
            try:
                await websocket.send(json.dumps(message))
            except websockets.exceptions.ConnectionClosed:
                disconnected.add(websocket)
            except Exception as e:
                logger.error(f"Error broadcasting to topic subscriber: {e}")
                disconnected.add(websocket)
                
        # Clean up disconnected clients
        for websocket in disconnected:
            await self.unregister_client(websocket)
            
    async def handle_message(self, websocket, message: str):
        """Handle incoming message from client"""
        try:
            data = json.loads(message)
            message_type = data.get('type')
            payload = data.get('payload', {})
            
            if message_type == 'SUBSCRIBE':
                topic = payload.get('topic')
                if topic:
                    await self.subscribe_to_topic(websocket, topic)
                    
            elif message_type == 'UNSUBSCRIBE':
                topic = payload.get('topic')
                if topic:
                    await self.unsubscribe_from_topic(websocket, topic)
                    
            elif message_type == 'STOCK_UPDATE':
                await self.handle_stock_update(payload)
                
            elif message_type == 'HEARTBEAT':
                await self.send_to_client(websocket, {
                    'type': 'HEARTBEAT_ACK',
                    'payload': {'timestamp': datetime.now().isoformat()}
                })
                
            elif message_type == 'GET_STOCK_DATA':
                product_id = payload.get('productId')
                if product_id and product_id in self.stock_data:
                    await self.send_to_client(websocket, {
                        'type': 'STOCK_DATA',
                        'payload': self.stock_data[product_id]
                    })
                    
        except json.JSONDecodeError:
            logger.error("Invalid JSON received from client")
        except Exception as e:
            logger.error(f"Error handling message: {e}")
            
    async def handle_stock_update(self, payload: dict):
        """Handle stock update and broadcast to relevant clients"""
        try:
            product_id = payload.get('productId')
            if not product_id:
                return
                
            # Update local stock data
            if product_id not in self.stock_data:
                self.stock_data[product_id] = {}
                
            self.stock_data[product_id].update(payload)
            self.stock_data[product_id]['lastUpdated'] = datetime.now().isoformat()
            
            # Broadcast stock update
            update_message = {
                'type': 'STOCK_UPDATED',
                'payload': payload
            }
            
            await self.broadcast_to_all(update_message)
            
            # Check for alerts
            await self.check_and_send_alerts(payload)
            
            logger.info(f"Stock updated for product {product_id}: {payload.get('newStock', 'unknown')} units")
            
        except Exception as e:
            logger.error(f"Error handling stock update: {e}")
            
    async def check_and_send_alerts(self, stock_data: dict):
        """Check stock levels and send alerts if necessary"""
        try:
            product_id = stock_data.get('productId')
            current_stock = stock_data.get('newStock', 0)
            reorder_point = stock_data.get('reorderPoint', 0)
            product_name = stock_data.get('productName', f'Product {product_id}')
            
            # Check for low stock
            if current_stock <= 0:
                alert = {
                    'type': 'ALERT_GENERATED',
                    'payload': {
                        'id': f"{product_id}-out-of-stock-{datetime.now().timestamp()}",
                        'productId': product_id,
                        'productName': product_name,
                        'alertType': 'OUT_OF_STOCK',
                        'severity': 'critical',
                        'message': f'{product_name} is out of stock',
                        'currentStock': current_stock,
                        'timestamp': datetime.now().isoformat()
                    }
                }
                await self.broadcast_to_topic('alerts', alert)
                
            elif current_stock <= reorder_point:
                alert = {
                    'type': 'ALERT_GENERATED',
                    'payload': {
                        'id': f"{product_id}-low-stock-{datetime.now().timestamp()}",
                        'productId': product_id,
                        'productName': product_name,
                        'alertType': 'LOW_STOCK',
                        'severity': 'warning',
                        'message': f'{product_name} is below reorder point ({current_stock}/{reorder_point})',
                        'currentStock': current_stock,
                        'reorderPoint': reorder_point,
                        'timestamp': datetime.now().isoformat()
                    }
                }
                await self.broadcast_to_topic('alerts', alert)
                
        except Exception as e:
            logger.error(f"Error checking alerts: {e}")
            
    async def send_periodic_updates(self):
        """Send periodic stock updates and heartbeats"""
        while True:
            try:
                # Send heartbeat to all clients
                heartbeat = {
                    'type': 'HEARTBEAT',
                    'payload': {
                        'timestamp': datetime.now().isoformat(),
                        'serverStatus': 'healthy',
                        'connectedClients': len(self.clients)
                    }
                }
                await self.broadcast_to_all(heartbeat)
                
                # Send stock summary
                if self.stock_data:
                    summary = {
                        'type': 'STOCK_SUMMARY',
                        'payload': {
                            'totalProducts': len(self.stock_data),
                            'lastUpdate': datetime.now().isoformat(),
                            'products': list(self.stock_data.values())
                        }
                    }
                    await self.broadcast_to_topic('stock-summary', summary)
                
                await asyncio.sleep(30)  # Send updates every 30 seconds
                
            except Exception as e:
                logger.error(f"Error in periodic updates: {e}")
                await asyncio.sleep(5)
                
    async def handle_client(self, websocket, path):
        """Handle new client connection"""
        await self.register_client(websocket)
        
        try:
            async for message in websocket:
                await self.handle_message(websocket, message)
        except websockets.exceptions.ConnectionClosed:
            pass
        except Exception as e:
            logger.error(f"Error in client handler: {e}")
        finally:
            await self.unregister_client(websocket)
            
    async def start_server(self):
        """Start the WebSocket server"""
        logger.info(f"Starting Stock WebSocket server on {self.host}:{self.port}")
        
        # Start periodic updates task
        asyncio.create_task(self.send_periodic_updates())
        
        # Start WebSocket server
        server = await websockets.serve(
            self.handle_client,
            self.host,
            self.port,
            ping_interval=20,
            ping_timeout=10
        )
        
        logger.info(f"Stock WebSocket server started on ws://{self.host}:{self.port}")
        return server
        
    def simulate_stock_updates(self):
        """Simulate stock updates for testing"""
        async def simulate():
            await asyncio.sleep(5)  # Wait for server to start
            
            # Sample products for simulation
            sample_products = [
                {
                    'productId': 1,
                    'productName': 'Product A',
                    'barcode': '1234567890123',
                    'reorderPoint': 50,
                    'location': 'Warehouse A'
                },
                {
                    'productId': 2,
                    'productName': 'Product B',
                    'barcode': '2345678901234',
                    'reorderPoint': 75,
                    'location': 'Warehouse B'
                }
            ]
            
            import random
            counter = 0
            
            while True:
                try:
                    # Simulate random stock updates
                    product = random.choice(sample_products)
                    old_stock = random.randint(0, 200)
                    change = random.randint(-20, 30)
                    new_stock = max(0, old_stock + change)
                    
                    update_payload = {
                        **product,
                        'oldStock': old_stock,
                        'newStock': new_stock,
                        'change': change,
                        'operation': 'UPDATE',
                        'timestamp': datetime.now().isoformat(),
                        'source': 'simulation'
                    }
                    
                    await self.handle_stock_update(update_payload)
                    
                    counter += 1
                    logger.info(f"Simulated update #{counter}: {product['productName']} stock changed from {old_stock} to {new_stock}")
                    
                    await asyncio.sleep(random.randint(10, 30))  # Random interval between 10-30 seconds
                    
                except Exception as e:
                    logger.error(f"Error in stock simulation: {e}")
                    await asyncio.sleep(5)
                    
        return asyncio.create_task(simulate())

# API Integration Functions
async def integrate_with_laravel_api():
    """Integrate with Laravel API for real stock data"""
    # This would connect to your Laravel API endpoints
    # For now, we'll use mock data
    pass

# Main execution
async def main():
    # Create and start the server
    server = StockWebSocketServer()
    
    # Start the WebSocket server
    websocket_server = await server.start_server()
    
    # Start stock simulation for testing
    simulation_task = server.simulate_stock_updates()
    
    try:
        # Keep the server running
        await websocket_server.wait_closed()
    except KeyboardInterrupt:
        logger.info("Server shutdown requested")
    finally:
        simulation_task.cancel()
        websocket_server.close()
        await websocket_server.wait_closed()
        logger.info("Server shutdown complete")

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\nServer stopped by user")
    except Exception as e:
        print(f"Server error: {e}")
