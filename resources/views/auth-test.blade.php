<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Test - Stoir API</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-8">🔐 Authentication System Test</h1>
        
        <!-- Login Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Login</h2>
            <form id="loginForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Division Code</label>
                    <input type="text" id="kodedivisi" value="01" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" value="admin" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" value="admin123" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
                <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Login</button>
            </form>
        </div>

        <!-- Token Display -->
        <div id="tokenSection" class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 hidden">
            <h3 class="text-lg font-semibold text-green-800 mb-2">Authentication Token</h3>
            <p id="tokenDisplay" class="text-sm text-green-700 break-all"></p>
        </div>

        <!-- User Info -->
        <div id="userSection" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 hidden">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">User Information</h3>
            <pre id="userInfo" class="text-sm text-blue-700"></pre>
        </div>

        <!-- API Tests -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">API Tests</h2>
            <div class="space-y-3">
                <button id="testMe" class="w-full bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 disabled:opacity-50" disabled>
                    Test /api/auth/me
                </button>
                <button id="testLogout" class="w-full bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 disabled:opacity-50" disabled>
                    Test Logout
                </button>
                <button id="testProtected" class="w-full bg-purple-500 text-white py-2 px-4 rounded-md hover:bg-purple-600 disabled:opacity-50" disabled>
                    Test Protected Route (/api/areas)
                </button>
            </div>
        </div>

        <!-- Response Log -->
        <div class="bg-gray-50 rounded-lg p-4 mt-6">
            <h3 class="text-lg font-semibold mb-2">Response Log</h3>
            <div id="responseLog" class="text-sm font-mono max-h-64 overflow-y-auto"></div>
        </div>
    </div>

    <script>
        let currentToken = null;
        const baseUrl = window.location.origin;

        function log(message, type = 'info') {
            const logDiv = document.getElementById('responseLog');
            const timestamp = new Date().toLocaleTimeString();
            const colorClass = type === 'error' ? 'text-red-600' : type === 'success' ? 'text-green-600' : 'text-gray-600';
            logDiv.innerHTML += `<div class="${colorClass}">[${timestamp}] ${message}</div>`;
            logDiv.scrollTop = logDiv.scrollHeight;
        }

        function updateUI(loggedIn = false) {
            const buttons = ['testMe', 'testLogout', 'testProtected'];
            buttons.forEach(id => {
                document.getElementById(id).disabled = !loggedIn;
            });
        }

        // Login Form
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = {
                kodedivisi: document.getElementById('kodedivisi').value,
                username: document.getElementById('username').value,
                password: document.getElementById('password').value
            };

            try {
                log('Attempting login...', 'info');
                const response = await fetch(`${baseUrl}/api/auth/login`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();
                
                if (response.ok) {
                    currentToken = data.data.token;
                    document.getElementById('tokenDisplay').textContent = currentToken;
                    document.getElementById('tokenSection').classList.remove('hidden');
                    
                    document.getElementById('userInfo').textContent = JSON.stringify(data.data.user, null, 2);
                    document.getElementById('userSection').classList.remove('hidden');
                    
                    updateUI(true);
                    log('✅ Login successful!', 'success');
                } else {
                    log(`❌ Login failed: ${JSON.stringify(data)}`, 'error');
                }
            } catch (error) {
                log(`❌ Network error: ${error.message}`, 'error');
            }
        });

        // Test /api/auth/me
        document.getElementById('testMe').addEventListener('click', async () => {
            try {
                log('Testing /api/auth/me...', 'info');
                const response = await fetch(`${baseUrl}/api/auth/me`, {
                    headers: {
                        'Authorization': `Bearer ${currentToken}`,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                log(`Response: ${JSON.stringify(data)}`, response.ok ? 'success' : 'error');
            } catch (error) {
                log(`❌ Error: ${error.message}`, 'error');
            }
        });

        // Test Logout
        document.getElementById('testLogout').addEventListener('click', async () => {
            try {
                log('Testing logout...', 'info');
                const response = await fetch(`${baseUrl}/api/auth/logout`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${currentToken}`,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (response.ok) {
                    currentToken = null;
                    document.getElementById('tokenSection').classList.add('hidden');
                    document.getElementById('userSection').classList.add('hidden');
                    updateUI(false);
                    log('✅ Logout successful!', 'success');
                } else {
                    log(`❌ Logout failed: ${JSON.stringify(data)}`, 'error');
                }
            } catch (error) {
                log(`❌ Error: ${error.message}`, 'error');
            }
        });

        // Test Protected Route
        document.getElementById('testProtected').addEventListener('click', async () => {
            try {
                log('Testing protected route /api/areas...', 'info');
                const response = await fetch(`${baseUrl}/api/areas`, {
                    headers: {
                        'Authorization': `Bearer ${currentToken}`,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                log(`Response: ${JSON.stringify(data)}`, response.ok ? 'success' : 'error');
            } catch (error) {
                log(`❌ Error: ${error.message}`, 'error');
            }
        });

        log('🚀 Authentication test page loaded');
    </script>
</body>
</html>
