import React, { useState, useEffect, useMemo } from 'react';
import {
  Box,
  Grid,
  Card,
  CardContent,
  Typography,
  IconButton,
  List,
  ListItem,
  ListItemText,
  ListItemIcon,
  Button,
  Chip,
  Divider,
  Avatar,
  LinearProgress,
  Alert,
  Tooltip,
  Paper,
  useTheme,
  useMediaQuery
} from '@mui/material';
import {
  TrendingUp,
  TrendingDown,
  Inventory,
  AttachMoney,
  ShoppingCart,
  Person,
  Warning,
  CheckCircle,
  Refresh,
  AddCircle,
  Assignment,
  AccountBalance,
  ShowChart,
  PieChart,
  BarChart,
  Timeline,
  Notifications,
  Launch
} from '@mui/icons-material';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip as ChartTooltip,
  Legend,
  ArcElement
} from 'chart.js';
import { Line, Bar, Doughnut } from 'react-chartjs-2';
import { format, startOfMonth, endOfMonth, subMonths } from 'date-fns';
import { useNavigate } from 'react-router-dom';
import { apiUtils, endpoints } from '../../utils/apiUtils';
import { PageLoading, LoadingSpinner } from '../../components/LoadingComponents';
import { useResponsive } from '../../components/ResponsiveUtils';

// Register ChartJS components
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  ChartTooltip,
  Legend,
  ArcElement
);

const Dashboard = () => {
  const theme = useTheme();
  const navigate = useNavigate();
  const { isMobile, isTablet } = useResponsive();
  
  // State management
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [refreshing, setRefreshing] = useState(false);
  const [lastUpdated, setLastUpdated] = useState(new Date());
  
  // Dashboard data state
  const [dashboardData, setDashboardData] = useState({
    kpis: {
      totalSales: { value: 0, change: 0, period: 'vs last month' },
      totalInventory: { value: 0, change: 0, period: 'items in stock' },
      totalCustomers: { value: 0, change: 0, period: 'active customers' },
      totalRevenue: { value: 0, change: 0, period: 'this month' },
      pendingOrders: { value: 0, change: 0, period: 'pending orders' },
      lowStock: { value: 0, change: 0, period: 'items low stock' },
      overduePayments: { value: 0, change: 0, period: 'overdue payments' },
      profitMargin: { value: 0, change: 0, period: 'profit margin' }
    },
    charts: {
      salesTrend: [],
      topProducts: [],
      monthlyComparison: [],
      inventoryByCategory: []
    },
    recentActivities: [],
    alerts: []
  });

  // Fetch dashboard data
  const fetchDashboardData = async () => {
    try {
      setError(null);
      
      // Try to fetch real data, fallback to sample data
      const [
        salesData,
        inventoryData,
        customersData,
        ordersData,
        activitiesData
      ] = await Promise.allSettled([
        apiUtils.get('/dashboard/sales-summary'),
        apiUtils.get('/dashboard/inventory-summary'),
        apiUtils.get('/dashboard/customers-summary'),
        apiUtils.get('/dashboard/orders-summary'),
        apiUtils.get('/dashboard/recent-activities')
      ]);

      // Process KPIs
      const kpis = {
        totalSales: {
          value: salesData.value?.total_sales || 125000000,
          change: salesData.value?.sales_growth || 12.5,
          period: 'vs last month'
        },
        totalInventory: {
          value: inventoryData.value?.total_items || 1547,
          change: inventoryData.value?.inventory_change || -3.2,
          period: 'items in stock'
        },
        totalCustomers: {
          value: customersData.value?.total_customers || 342,
          change: customersData.value?.customer_growth || 8.7,
          period: 'active customers'
        },
        totalRevenue: {
          value: salesData.value?.monthly_revenue || 87500000,
          change: salesData.value?.revenue_growth || 15.2,
          period: 'this month'
        },
        pendingOrders: {
          value: ordersData.value?.pending_orders || 23,
          change: ordersData.value?.pending_change || -18.5,
          period: 'pending orders'
        },
        lowStock: {
          value: inventoryData.value?.low_stock_items || 12,
          change: inventoryData.value?.low_stock_change || 25.0,
          period: 'items low stock'
        },
        overduePayments: {
          value: salesData.value?.overdue_payments || 5,
          change: salesData.value?.overdue_change || -20.0,
          period: 'overdue payments'
        },
        profitMargin: {
          value: salesData.value?.profit_margin || 23.5,
          change: salesData.value?.margin_change || 2.1,
          period: 'profit margin'
        }
      };

      // Process charts data
      const charts = {
        salesTrend: salesData.value?.sales_trend || generateSampleSalesTrend(),
        topProducts: inventoryData.value?.top_products || generateSampleTopProducts(),
        monthlyComparison: salesData.value?.monthly_comparison || generateSampleMonthlyComparison(),
        inventoryByCategory: inventoryData.value?.category_breakdown || generateSampleCategoryBreakdown()
      };

      // Process recent activities
      const recentActivities = activitiesData.value?.activities || generateSampleActivities();

      // Generate alerts
      const alerts = generateAlerts(kpis);

      setDashboardData({
        kpis,
        charts,
        recentActivities,
        alerts
      });

      setLastUpdated(new Date());
    } catch (error) {
      console.error('Error fetching dashboard data:', error);
      setError('Failed to load dashboard data. Using sample data.');
      
      // Fallback to sample data
      setDashboardData({
        kpis: generateSampleKPIs(),
        charts: {
          salesTrend: generateSampleSalesTrend(),
          topProducts: generateSampleTopProducts(),
          monthlyComparison: generateSampleMonthlyComparison(),
          inventoryByCategory: generateSampleCategoryBreakdown()
        },
        recentActivities: generateSampleActivities(),
        alerts: generateSampleAlerts()
      });
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  // Sample data generators
  const generateSampleKPIs = () => ({
    totalSales: { value: 125000000, change: 12.5, period: 'vs last month' },
    totalInventory: { value: 1547, change: -3.2, period: 'items in stock' },
    totalCustomers: { value: 342, change: 8.7, period: 'active customers' },
    totalRevenue: { value: 87500000, change: 15.2, period: 'this month' },
    pendingOrders: { value: 23, change: -18.5, period: 'pending orders' },
    lowStock: { value: 12, change: 25.0, period: 'items low stock' },
    overduePayments: { value: 5, change: -20.0, period: 'overdue payments' },
    profitMargin: { value: 23.5, change: 2.1, period: 'profit margin' }
  });

  const generateSampleSalesTrend = () => {
    const months = [];
    const sales = [];
    for (let i = 11; i >= 0; i--) {
      const date = subMonths(new Date(), i);
      months.push(format(date, 'MMM'));
      sales.push(Math.floor(Math.random() * 50000000) + 30000000);
    }
    return { labels: months, data: sales };
  };

  const generateSampleTopProducts = () => [
    { name: 'Oli Mesin Shell 1L', sales: 245, revenue: 12250000 },
    { name: 'Ban Motor Bridgestone', sales: 189, revenue: 18900000 },
    { name: 'Kampas Rem Honda', sales: 156, revenue: 4680000 },
    { name: 'Filter Udara Toyota', sales: 134, revenue: 2680000 },
    { name: 'Aki Motor GS', sales: 98, revenue: 9800000 }
  ];

  const generateSampleMonthlyComparison = () => ({
    labels: ['Sales', 'Purchases', 'Returns', 'Profit'],
    thisMonth: [87500000, 65000000, 2500000, 20000000],
    lastMonth: [75000000, 58000000, 3200000, 16800000]
  });

  const generateSampleCategoryBreakdown = () => ({
    labels: ['Oli & Pelumas', 'Spare Part', 'Aksesoris', 'Ban & Velg', 'Elektronik'],
    data: [450, 320, 180, 250, 125],
    colors: ['#1976d2', '#388e3c', '#f57c00', '#d32f2f', '#7b1fa2']
  });

  const generateSampleActivities = () => [
    { id: 1, type: 'sale', title: 'New sale order #INV-2024-001', customer: 'PT Maju Motor', amount: 2500000, time: '5 minutes ago' },
    { id: 2, type: 'inventory', title: 'Low stock alert', product: 'Oli Shell 1L', stock: 5, time: '12 minutes ago' },
    { id: 3, type: 'payment', title: 'Payment received', customer: 'CV Auto Parts', amount: 5000000, time: '1 hour ago' },
    { id: 4, type: 'purchase', title: 'Purchase order created', supplier: 'PT Supplier ABC', amount: 15000000, time: '2 hours ago' },
    { id: 5, type: 'customer', title: 'New customer registered', customer: 'Bengkel Jaya Motor', time: '3 hours ago' }
  ];

  const generateAlerts = (kpis) => {
    const alerts = [];
    if (kpis.lowStock.value > 10) {
      alerts.push({ id: 1, type: 'warning', title: 'Low Stock Items', message: `${kpis.lowStock.value} items are running low on stock`, action: 'View Items' });
    }
    if (kpis.overduePayments.value > 0) {
      alerts.push({ id: 2, type: 'error', title: 'Overdue Payments', message: `${kpis.overduePayments.value} payments are overdue`, action: 'View Payments' });
    }
    if (kpis.pendingOrders.value > 20) {
      alerts.push({ id: 3, type: 'info', title: 'Pending Orders', message: `${kpis.pendingOrders.value} orders are waiting for processing`, action: 'Process Orders' });
    }
    return alerts;
  };

  const generateSampleAlerts = () => [
    { id: 1, type: 'warning', title: 'Low Stock Items', message: '12 items are running low on stock', action: 'View Items' },
    { id: 2, type: 'error', title: 'Overdue Payments', message: '5 payments are overdue', action: 'View Payments' },
    { id: 3, type: 'info', title: 'Pending Orders', message: '23 orders are waiting for processing', action: 'Process Orders' }
  ];

  // Refresh handler
  const handleRefresh = async () => {
    setRefreshing(true);
    await fetchDashboardData();
  };

  // Initial load
  useEffect(() => {
    fetchDashboardData();
    
    // Auto-refresh every 5 minutes
    const interval = setInterval(fetchDashboardData, 300000);
    return () => clearInterval(interval);
  }, []);

  // Chart configurations
  const salesTrendConfig = useMemo(() => ({
    data: {
      labels: dashboardData.charts.salesTrend.labels || [],
      datasets: [
        {
          label: 'Sales (Rp)',
          data: dashboardData.charts.salesTrend.data || [],
          borderColor: theme.palette.primary.main,
          backgroundColor: theme.palette.primary.light + '20',
          fill: true,
          tension: 0.4
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        title: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: (value) => `Rp ${(value / 1000000).toFixed(0)}M`
          }
        }
      }
    }
  }), [dashboardData.charts.salesTrend, theme]);

  const monthlyComparisonConfig = useMemo(() => ({
    data: {
      labels: dashboardData.charts.monthlyComparison.labels || [],
      datasets: [
        {
          label: 'This Month',
          data: dashboardData.charts.monthlyComparison.thisMonth || [],
          backgroundColor: theme.palette.primary.main,
          borderRadius: 4
        },
        {
          label: 'Last Month',
          data: dashboardData.charts.monthlyComparison.lastMonth || [],
          backgroundColor: theme.palette.grey[400],
          borderRadius: 4
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'top' }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: (value) => `Rp ${(value / 1000000).toFixed(0)}M`
          }
        }
      }
    }
  }), [dashboardData.charts.monthlyComparison, theme]);

  const categoryBreakdownConfig = useMemo(() => ({
    data: {
      labels: dashboardData.charts.inventoryByCategory.labels || [],
      datasets: [
        {
          data: dashboardData.charts.inventoryByCategory.data || [],
          backgroundColor: dashboardData.charts.inventoryByCategory.colors || [
            theme.palette.primary.main,
            theme.palette.secondary.main,
            theme.palette.success.main,
            theme.palette.warning.main,
            theme.palette.error.main
          ]
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: isMobile ? 'bottom' : 'right' }
      }
    }
  }), [dashboardData.charts.inventoryByCategory, theme, isMobile]);

  // Format currency
  const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(value);
  };

  // Format number
  const formatNumber = (value) => {
    return new Intl.NumberFormat('id-ID').format(value);
  };

  // Loading state
  if (loading) {
    return <PageLoading />;
  }

  return (
    <Box sx={{ flexGrow: 1, p: 3 }}>
      {/* Header */}
      <Box display="flex" justifyContent="space-between" alignItems="center" mb={3}>
        <Box>
          <Typography variant="h4" component="h1" gutterBottom>
            🎯 StockFlow Dashboard PowerHouse
          </Typography>
          <Typography variant="body2" color="text.secondary">
            Last updated: {format(lastUpdated, 'dd MMM yyyy, HH:mm')}
          </Typography>
        </Box>
        <Box display="flex" gap={1}>
          <Tooltip title="Refresh Data">
            <IconButton 
              onClick={handleRefresh} 
              disabled={refreshing}
              color="primary"
            >
              {refreshing ? <LoadingSpinner size={24} /> : <Refresh />}
            </IconButton>
          </Tooltip>
        </Box>
      </Box>

      {/* Error Alert */}
      {error && (
        <Alert severity="warning" sx={{ mb: 3 }} onClose={() => setError(null)}>
          {error}
        </Alert>
      )}

      {/* Alerts Section */}
      {dashboardData.alerts.length > 0 && (
        <Box mb={3}>
          <Typography variant="h6" gutterBottom sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
            <Notifications color="warning" />
            Business Alerts & Notifications
          </Typography>
          <Grid container spacing={2}>
            {dashboardData.alerts.map((alert) => (
              <Grid item xs={12} md={4} key={alert.id}>
                <Alert 
                  severity={alert.type} 
                  action={
                    <Button size="small" color="inherit">
                      {alert.action}
                    </Button>
                  }
                >
                  <Typography variant="subtitle2">{alert.title}</Typography>
                  <Typography variant="body2">{alert.message}</Typography>
                </Alert>
              </Grid>
            ))}
          </Grid>
        </Box>
      )}

      {/* KPI Cards */}
      <Box mb={4}>
        <Typography variant="h6" gutterBottom>
          📊 Key Performance Indicators
        </Typography>
        <Grid container spacing={3}>
          {/* Sales KPI */}
          <Grid item xs={12} sm={6} md={3}>
            <KPICard
              title="Total Sales"
              value={formatCurrency(dashboardData.kpis.totalSales.value)}
              change={dashboardData.kpis.totalSales.change}
              period={dashboardData.kpis.totalSales.period}
              icon={<AttachMoney />}
              color="primary"
              onClick={() => navigate('/reports/sales')}
            />
          </Grid>

          {/* Inventory KPI */}
          <Grid item xs={12} sm={6} md={3}>
            <KPICard
              title="Inventory Items"
              value={formatNumber(dashboardData.kpis.totalInventory.value)}
              change={dashboardData.kpis.totalInventory.change}
              period={dashboardData.kpis.totalInventory.period}
              icon={<Inventory />}
              color="secondary"
              onClick={() => navigate('/reports/inventory')}
            />
          </Grid>

          {/* Customers KPI */}
          <Grid item xs={12} sm={6} md={3}>
            <KPICard
              title="Active Customers"
              value={formatNumber(dashboardData.kpis.totalCustomers.value)}
              change={dashboardData.kpis.totalCustomers.change}
              period={dashboardData.kpis.totalCustomers.period}
              icon={<Person />}
              color="success"
              onClick={() => navigate('/master/customer')}
            />
          </Grid>

          {/* Revenue KPI */}
          <Grid item xs={12} sm={6} md={3}>
            <KPICard
              title="Monthly Revenue"
              value={formatCurrency(dashboardData.kpis.totalRevenue.value)}
              change={dashboardData.kpis.totalRevenue.change}
              period={dashboardData.kpis.totalRevenue.period}
              icon={<ShowChart />}
              color="info"
              onClick={() => navigate('/reports/financial')}
            />
          </Grid>

          {/* Additional KPIs for larger screens */}
          {!isMobile && (
            <>
              <Grid item xs={12} sm={6} md={3}>
                <KPICard
                  title="Pending Orders"
                  value={formatNumber(dashboardData.kpis.pendingOrders.value)}
                  change={dashboardData.kpis.pendingOrders.change}
                  period={dashboardData.kpis.pendingOrders.period}
                  icon={<ShoppingCart />}
                  color="warning"
                  onClick={() => navigate('/transactions/sales')}
                />
              </Grid>

              <Grid item xs={12} sm={6} md={3}>
                <KPICard
                  title="Low Stock Items"
                  value={formatNumber(dashboardData.kpis.lowStock.value)}
                  change={dashboardData.kpis.lowStock.change}
                  period={dashboardData.kpis.lowStock.period}
                  icon={<Warning />}
                  color="error"
                  onClick={() => navigate('/reports/stock-minimum')}
                />
              </Grid>

              <Grid item xs={12} sm={6} md={3}>
                <KPICard
                  title="Overdue Payments"
                  value={formatNumber(dashboardData.kpis.overduePayments.value)}
                  change={dashboardData.kpis.overduePayments.change}
                  period={dashboardData.kpis.overduePayments.period}
                  icon={<AccountBalance />}
                  color="error"
                  onClick={() => navigate('/finance/receivables')}
                />
              </Grid>

              <Grid item xs={12} sm={6} md={3}>
                <KPICard
                  title="Profit Margin"
                  value={`${dashboardData.kpis.profitMargin.value}%`}
                  change={dashboardData.kpis.profitMargin.change}
                  period={dashboardData.kpis.profitMargin.period}
                  icon={<TrendingUp />}
                  color="success"
                  onClick={() => navigate('/reports/profit-loss')}
                />
              </Grid>
            </>
          )}
        </Grid>
      </Box>

      {/* Charts Section */}
      <Grid container spacing={3} mb={4}>
        {/* Sales Trend Chart */}
        <Grid item xs={12} lg={8}>
          <Card>
            <CardContent>
              <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
                <Typography variant="h6" component="h2">
                  📈 Sales Trend (12 Months)
                </Typography>
                <IconButton size="small" onClick={() => navigate('/reports/sales')}>
                  <Launch />
                </IconButton>
              </Box>
              <Box height={300}>
                <Line {...salesTrendConfig} />
              </Box>
            </CardContent>
          </Card>
        </Grid>

        {/* Inventory Breakdown */}
        <Grid item xs={12} lg={4}>
          <Card>
            <CardContent>
              <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
                <Typography variant="h6" component="h2">
                  🥧 Inventory by Category
                </Typography>
                <IconButton size="small" onClick={() => navigate('/reports/inventory')}>
                  <Launch />
                </IconButton>
              </Box>
              <Box height={300}>
                <Doughnut {...categoryBreakdownConfig} />
              </Box>
            </CardContent>
          </Card>
        </Grid>

        {/* Monthly Comparison */}
        <Grid item xs={12} lg={6}>
          <Card>
            <CardContent>
              <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
                <Typography variant="h6" component="h2">
                  📊 Monthly Comparison
                </Typography>
                <IconButton size="small" onClick={() => navigate('/reports/monthly-comparison')}>
                  <Launch />
                </IconButton>
              </Box>
              <Box height={300}>
                <Bar {...monthlyComparisonConfig} />
              </Box>
            </CardContent>
          </Card>
        </Grid>

        {/* Top Products */}
        <Grid item xs={12} lg={6}>
          <Card>
            <CardContent>
              <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
                <Typography variant="h6" component="h2">
                  🏆 Top Selling Products
                </Typography>
                <IconButton size="small" onClick={() => navigate('/reports/top-products')}>
                  <Launch />
                </IconButton>
              </Box>
              <List dense>
                {dashboardData.charts.topProducts.map((product, index) => (
                  <ListItem key={index} divider>
                    <ListItemIcon>
                      <Avatar sx={{ bgcolor: theme.palette.primary.main, width: 32, height: 32, fontSize: 14 }}>
                        {index + 1}
                      </Avatar>
                    </ListItemIcon>
                    <ListItemText
                      primary={product.name}
                      secondary={
                        <Box display="flex" justifyContent="space-between">
                          <Typography variant="body2">
                            {product.sales} units sold
                          </Typography>
                          <Typography variant="body2" fontWeight="bold">
                            {formatCurrency(product.revenue)}
                          </Typography>
                        </Box>
                      }
                    />
                  </ListItem>
                ))}
              </List>
            </CardContent>
          </Card>
        </Grid>
      </Grid>

      {/* Bottom Section */}
      <Grid container spacing={3}>
        {/* Recent Activities */}
        <Grid item xs={12} lg={8}>
          <Card>
            <CardContent>
              <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
                <Typography variant="h6" component="h2">
                  🕒 Recent Activities
                </Typography>
                <Button 
                  size="small" 
                  onClick={() => navigate('/activities')}
                  endIcon={<Launch />}
                >
                  View All
                </Button>
              </Box>
              <List>
                {dashboardData.recentActivities.map((activity) => (
                  <ListItem key={activity.id} divider>
                    <ListItemIcon>
                      <ActivityIcon type={activity.type} />
                    </ListItemIcon>
                    <ListItemText
                      primary={activity.title}
                      secondary={
                        <Box>
                          {activity.customer && (
                            <Typography variant="body2" color="text.secondary">
                              Customer: {activity.customer}
                            </Typography>
                          )}
                          {activity.amount && (
                            <Typography variant="body2" color="primary">
                              Amount: {formatCurrency(activity.amount)}
                            </Typography>
                          )}
                          <Typography variant="caption" color="text.secondary">
                            {activity.time}
                          </Typography>
                        </Box>
                      }
                    />
                  </ListItem>
                ))}
              </List>
            </CardContent>
          </Card>
        </Grid>

        {/* Quick Actions */}
        <Grid item xs={12} lg={4}>
          <Card>
            <CardContent>
              <Typography variant="h6" component="h2" gutterBottom>
                ⚡ Quick Actions
              </Typography>
              <Grid container spacing={2}>
                <Grid item xs={6}>
                  <Button
                    fullWidth
                    variant="outlined"
                    startIcon={<AddCircle />}
                    onClick={() => navigate('/transactions/sales/form')}
                    sx={{ height: 60, flexDirection: 'column', gap: 1 }}
                  >
                    <Typography variant="caption">New Sale</Typography>
                  </Button>
                </Grid>
                <Grid item xs={6}>
                  <Button
                    fullWidth
                    variant="outlined"
                    startIcon={<ShoppingCart />}
                    onClick={() => navigate('/transactions/purchase/form')}
                    sx={{ height: 60, flexDirection: 'column', gap: 1 }}
                  >
                    <Typography variant="caption">New Purchase</Typography>
                  </Button>
                </Grid>
                <Grid item xs={6}>
                  <Button
                    fullWidth
                    variant="outlined"
                    startIcon={<Person />}
                    onClick={() => navigate('/master/customer')}
                    sx={{ height: 60, flexDirection: 'column', gap: 1 }}
                  >
                    <Typography variant="caption">Add Customer</Typography>
                  </Button>
                </Grid>
                <Grid item xs={6}>
                  <Button
                    fullWidth
                    variant="outlined"
                    startIcon={<Inventory />}
                    onClick={() => navigate('/master/barang')}
                    sx={{ height: 60, flexDirection: 'column', gap: 1 }}
                  >
                    <Typography variant="caption">Add Product</Typography>
                  </Button>
                </Grid>
                <Grid item xs={12}>
                  <Button
                    fullWidth
                    variant="contained"
                    startIcon={<BarChart />}
                    onClick={() => navigate('/reports')}
                    sx={{ height: 50 }}
                  >
                    View All Reports
                  </Button>
                </Grid>
              </Grid>
            </CardContent>
          </Card>
        </Grid>
      </Grid>
    </Box>
  );
};

// KPI Card Component
const KPICard = ({ title, value, change, period, icon, color, onClick }) => {
  const theme = useTheme();
  
  const isPositive = change >= 0;
  const changeColor = isPositive ? theme.palette.success.main : theme.palette.error.main;
  const ChangeIcon = isPositive ? TrendingUp : TrendingDown;

  return (
    <Card 
      sx={{ 
        cursor: onClick ? 'pointer' : 'default',
        transition: 'transform 0.2s, box-shadow 0.2s',
        '&:hover': onClick ? {
          transform: 'translateY(-2px)',
          boxShadow: theme.shadows[4]
        } : {}
      }}
      onClick={onClick}
    >
      <CardContent>
        <Box display="flex" justifyContent="space-between" alignItems="flex-start">
          <Box>
            <Typography color="text.secondary" gutterBottom variant="body2">
              {title}
            </Typography>
            <Typography variant="h5" component="div" fontWeight="bold">
              {value}
            </Typography>
            <Box display="flex" alignItems="center" mt={1}>
              <ChangeIcon sx={{ fontSize: 16, color: changeColor, mr: 0.5 }} />
              <Typography variant="body2" sx={{ color: changeColor, mr: 1 }}>
                {Math.abs(change)}%
              </Typography>
              <Typography variant="caption" color="text.secondary">
                {period}
              </Typography>
            </Box>
          </Box>
          <Box
            sx={{
              backgroundColor: theme.palette[color].main + '20',
              borderRadius: '50%',
              p: 1,
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center'
            }}
          >
            {React.cloneElement(icon, { 
              sx: { color: theme.palette[color].main } 
            })}
          </Box>
        </Box>
      </CardContent>
    </Card>
  );
};

// Activity Icon Component
const ActivityIcon = ({ type }) => {
  const theme = useTheme();
  
  const iconProps = {
    sx: { 
      color: theme.palette.primary.main,
      backgroundColor: theme.palette.primary.main + '20',
      borderRadius: '50%',
      p: 0.5
    }
  };

  switch (type) {
    case 'sale':
      return <AttachMoney {...iconProps} />;
    case 'inventory':
      return <Inventory {...iconProps} />;
    case 'payment':
      return <AccountBalance {...iconProps} />;
    case 'purchase':
      return <ShoppingCart {...iconProps} />;
    case 'customer':
      return <Person {...iconProps} />;
    default:
      return <CheckCircle {...iconProps} />;
  }
};

export default Dashboard;
