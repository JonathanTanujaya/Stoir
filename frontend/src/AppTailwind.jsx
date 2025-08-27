import React, { useState, useEffect } from 'react';
import { Outlet, useLocation, useNavigate } from 'react-router-dom';
import {
  Box,
  Drawer,
  AppBar,
  Toolbar,
  Typography,
  IconButton,
  List,
  ListItem,
  ListItemButton,
  ListItemIcon,
  ListItemText,
  Collapse,
  Avatar,
  Menu,
  MenuItem,
  Divider,
  Breadcrumbs,
  Link,
  useTheme,
  useMediaQuery,
  Tooltip,
  Badge,
  Card,
  CardContent,
} from '@mui/material';
import { useResponsive } from './components/ResponsiveUtils';
import {
  Menu as MenuIcon,
  Dashboard as DashboardIcon,
  ExpandLess,
  ExpandMore,
  AccountCircle,
  Settings,
  Logout,
  Home,
  Business,
  ShoppingCart,
  Receipt,
  Assessment,
  AccountBalance,
  Category,
  People,
  Inventory,
  LocalShipping,
  MonetizationOn,
  TrendingUp,
  Store,
  Assignment,
  AttachMoney,
  BarChart,
  PieChart,
  Notifications,
  Search,
  FilterList,
} from '@mui/icons-material';

const drawerWidth = 280;

// Navigation structure based on existing routes
const navigationItems = [
  {
    title: 'Dashboard',
    icon: <DashboardIcon />,
    path: '/dashboard',
    exact: true,
  },
  {
    title: 'Master Data',
    icon: <Business />,
    children: [
      { title: 'Categories', path: '/master/categories', icon: <Category /> },
      { title: 'Customers', path: '/master/customer', icon: <People /> },
      { title: 'Suppliers', path: '/master/supplier', icon: <LocalShipping /> },
      { title: 'Products', path: '/master/barang', icon: <Inventory /> },
      { title: 'Sales Staff', path: '/master/sales', icon: <People /> },
      { title: 'Spareparts', path: '/master/sparepart', icon: <Settings /> },
      { title: 'Stock Minimum', path: '/master/stock-min', icon: <FilterList /> },
      { title: 'Areas', path: '/master/area', icon: <Business /> },
      { title: 'Checklist', path: '/master/checklist', icon: <Assignment /> },
      { title: 'Banks', path: '/master/bank', icon: <AccountBalance /> },
      { title: 'Bank Accounts', path: '/master/rekening', icon: <MonetizationOn /> },
    ],
  },
  {
    title: 'Transactions',
    icon: <ShoppingCart />,
    children: [
      { title: 'Purchasing', path: '/transactions/purchasing', icon: <ShoppingCart /> },
      { title: 'Purchase Form', path: '/transactions/purchasing/form', icon: <Receipt /> },
      { title: 'Purchase List', path: '/transactions/purchasing/list', icon: <Assignment /> },
      { title: 'Purchase Return', path: '/transactions/purchasing/return', icon: <TrendingUp /> },
      { title: 'Sales', path: '/transactions/sales', icon: <Store /> },
      { title: 'Sales Form', path: '/transactions/sales/form', icon: <Receipt /> },
      { title: 'Sales Return', path: '/transactions/sales/return', icon: <TrendingUp /> },
      { title: 'Merge Products', path: '/transactions/merge-barang', icon: <Settings /> },
      { title: 'Invoice Cancel', path: '/transactions/invoice-cancel', icon: <Receipt /> },
      { title: 'Stock Opname', path: '/transactions/stok-opname', icon: <Inventory /> },
      { title: 'Purchase Bonus', path: '/transactions/pembelian-bonus', icon: <MonetizationOn /> },
      { title: 'Sales Bonus', path: '/transactions/penjualan-bonus', icon: <MonetizationOn /> },
      { title: 'Customer Claim', path: '/transactions/customer-claim', icon: <Assignment /> },
      { title: 'Claim Return', path: '/transactions/pengembalian-claim', icon: <TrendingUp /> },
    ],
  },
  {
    title: 'Finance',
    icon: <AccountBalance />,
    children: [
      { title: 'Giro Receipt', path: '/finance/penerimaan-giro', icon: <Receipt /> },
      { title: 'Giro Search', path: '/finance/pencarian-giro', icon: <Search /> },
      { title: 'Delivery Receipt', path: '/finance/penerimaan-resi', icon: <LocalShipping /> },
      { title: 'Delivery Receivables', path: '/finance/piutang-resi', icon: <AttachMoney /> },
      { title: 'Return Receivables', path: '/finance/piutang-retur', icon: <TrendingUp /> },
      { title: 'Add Balance', path: '/finance/penambahan-saldo', icon: <MonetizationOn /> },
      { title: 'Reduce Balance', path: '/finance/pengurangan-saldo', icon: <MonetizationOn /> },
    ],
  },
  {
    title: 'Reports',
    icon: <Assessment />,
    children: [
      { title: 'Stock Report', path: '/reports/stok-barang', icon: <BarChart /> },
      { title: 'Stock Card', path: '/reports/kartu-stok', icon: <Assignment /> },
      { title: 'Purchase Report', path: '/reports/pembelian', icon: <ShoppingCart /> },
      { title: 'Purchase Items', path: '/reports/pembelian-item', icon: <Inventory /> },
      { title: 'Sales Report', path: '/reports/penjualan', icon: <Store /> },
      { title: 'COGS Report', path: '/reports/cogs', icon: <PieChart /> },
      { title: 'Sales Return', path: '/reports/return-sales', icon: <TrendingUp /> },
      { title: 'Invoice Display', path: '/reports/tampil-invoice', icon: <Receipt /> },
      { title: 'Account Balance', path: '/reports/saldo-rekening', icon: <AccountBalance /> },
      { title: 'Customer Payment', path: '/reports/pembayaran-customer', icon: <MonetizationOn /> },
      { title: 'Bills Report', path: '/reports/tagihan', icon: <Receipt /> },
      {
        title: 'Customer Return Deduction',
        path: '/reports/pemotongan-return-customer',
        icon: <TrendingUp />,
      },
      { title: 'Sales Commission', path: '/reports/komisi-sales', icon: <AttachMoney /> },
    ],
  },
];

const AppTailwind = () => {
  const [mobileOpen, setMobileOpen] = useState(false);
  const [expandedItems, setExpandedItems] = useState({});
  const [anchorEl, setAnchorEl] = useState(null);
  const [notifications, setNotifications] = useState(3); // Demo notification count

  const theme = useTheme();
  const isMobile = useMediaQuery(theme.breakpoints.down('lg'));
  const location = useLocation();
  const navigate = useNavigate();

  // Auto-expand active menu items
  useEffect(() => {
    const currentPath = location.pathname;
    const newExpanded = {};

    navigationItems.forEach((item, index) => {
      if (item.children) {
        const hasActiveChild = item.children.some(child => currentPath.startsWith(child.path));
        if (hasActiveChild) {
          newExpanded[index] = true;
        }
      }
    });

    setExpandedItems(newExpanded);
  }, [location.pathname]);

  const handleDrawerToggle = () => {
    setMobileOpen(!mobileOpen);
  };

  const handleExpandClick = index => {
    setExpandedItems(prev => ({
      ...prev,
      [index]: !prev[index],
    }));
  };

  const handleProfileMenuOpen = event => {
    setAnchorEl(event.currentTarget);
  };

  const handleProfileMenuClose = () => {
    setAnchorEl(null);
  };

  const handleNavigation = path => {
    navigate(path);
    if (isMobile) {
      setMobileOpen(false);
    }
  };

  const isActiveRoute = (path, exact = false) => {
    if (exact) {
      return location.pathname === path;
    }
    return location.pathname.startsWith(path);
  };

  const generateBreadcrumbs = () => {
    const pathSegments = location.pathname.split('/').filter(Boolean);
    const breadcrumbs = [{ label: 'Home', path: '/dashboard' }];

    let currentPath = '';
    pathSegments.forEach((segment, index) => {
      currentPath += `/${segment}`;

      // Find the corresponding navigation item
      let label = segment.charAt(0).toUpperCase() + segment.slice(1);

      // Search through navigation structure for better labels
      navigationItems.forEach(item => {
        if (item.path === currentPath) {
          label = item.title;
        }
        if (item.children) {
          item.children.forEach(child => {
            if (child.path === currentPath) {
              label = child.title;
            }
          });
        }
      });

      breadcrumbs.push({
        label: label.replace(/-/g, ' '),
        path: currentPath,
      });
    });

    return breadcrumbs.slice(1); // Remove duplicate home
  };

  const renderNavigationItem = (item, index) => {
    const hasChildren = item.children && item.children.length > 0;
    const isExpanded = expandedItems[index];
    const isActive = isActiveRoute(item.path, item.exact);

    if (hasChildren) {
      return (
        <React.Fragment key={index}>
          <ListItem disablePadding>
            <ListItemButton
              onClick={() => handleExpandClick(index)}
              sx={{
                minHeight: 48,
                px: 2.5,
                backgroundColor: isActive ? 'action.selected' : 'transparent',
                '&:hover': {
                  backgroundColor: 'action.hover',
                },
              }}
            >
              <ListItemIcon
                sx={{
                  minWidth: 0,
                  mr: 3,
                  justifyContent: 'center',
                  color: isActive ? 'primary.main' : 'text.secondary',
                }}
              >
                {item.icon}
              </ListItemIcon>
              <ListItemText
                primary={item.title}
                sx={{
                  opacity: 1,
                  '& .MuiListItemText-primary': {
                    fontWeight: isActive ? 600 : 400,
                    color: isActive ? 'primary.main' : 'text.primary',
                  },
                }}
              />
              {isExpanded ? <ExpandLess /> : <ExpandMore />}
            </ListItemButton>
          </ListItem>
          <Collapse in={isExpanded} timeout="auto" unmountOnExit>
            <List component="div" disablePadding>
              {item.children.map((child, childIndex) => {
                const isChildActive = isActiveRoute(child.path);
                return (
                  <ListItem key={childIndex} disablePadding>
                    <ListItemButton
                      onClick={() => handleNavigation(child.path)}
                      sx={{
                        pl: 6,
                        minHeight: 40,
                        backgroundColor: isChildActive ? 'primary.light' : 'transparent',
                        '&:hover': {
                          backgroundColor: isChildActive ? 'primary.light' : 'action.hover',
                        },
                      }}
                    >
                      <ListItemIcon
                        sx={{
                          minWidth: 0,
                          mr: 2,
                          justifyContent: 'center',
                          color: isChildActive ? 'primary.main' : 'text.secondary',
                          '& .MuiSvgIcon-root': {
                            fontSize: '1.2rem',
                          },
                        }}
                      >
                        {child.icon}
                      </ListItemIcon>
                      <ListItemText
                        primary={child.title}
                        sx={{
                          '& .MuiListItemText-primary': {
                            fontSize: '0.875rem',
                            fontWeight: isChildActive ? 600 : 400,
                            color: isChildActive ? 'primary.main' : 'text.primary',
                          },
                        }}
                      />
                    </ListItemButton>
                  </ListItem>
                );
              })}
            </List>
          </Collapse>
        </React.Fragment>
      );
    }

    return (
      <ListItem key={index} disablePadding>
        <ListItemButton
          onClick={() => handleNavigation(item.path)}
          sx={{
            minHeight: 48,
            px: 2.5,
            backgroundColor: isActive ? 'primary.light' : 'transparent',
            '&:hover': {
              backgroundColor: isActive ? 'primary.light' : 'action.hover',
            },
          }}
        >
          <ListItemIcon
            sx={{
              minWidth: 0,
              mr: 3,
              justifyContent: 'center',
              color: isActive ? 'primary.main' : 'text.secondary',
            }}
          >
            {item.icon}
          </ListItemIcon>
          <ListItemText
            primary={item.title}
            sx={{
              '& .MuiListItemText-primary': {
                fontWeight: isActive ? 600 : 400,
                color: isActive ? 'primary.main' : 'text.primary',
              },
            }}
          />
        </ListItemButton>
      </ListItem>
    );
  };

  const drawer = (
    <Box sx={{ height: '100%', display: 'flex', flexDirection: 'column' }}>
      {/* Logo Section */}
      <Box sx={{ p: 2, borderBottom: 1, borderColor: 'divider' }}>
        <Typography
          variant="h6"
          noWrap
          component="div"
          sx={{
            fontWeight: 'bold',
            color: 'primary.main',
            textAlign: 'center',
          }}
        >
          StockFlow ERP
        </Typography>
        <Typography
          variant="caption"
          sx={{
            display: 'block',
            textAlign: 'center',
            color: 'text.secondary',
            mt: 0.5,
          }}
        >
          Inventory Management System
        </Typography>
      </Box>

      {/* Navigation Menu */}
      <Box sx={{ flexGrow: 1, overflow: 'auto' }}>
        <List sx={{ pt: 1 }}>
          {navigationItems.map((item, index) => renderNavigationItem(item, index))}
        </List>
      </Box>

      {/* Footer Section */}
      <Box sx={{ p: 2, borderTop: 1, borderColor: 'divider' }}>
        <Card variant="outlined" sx={{ bgcolor: 'background.paper' }}>
          <CardContent sx={{ p: 1.5, '&:last-child': { pb: 1.5 } }}>
            <Typography variant="caption" color="text.secondary">
              System Status: Online
            </Typography>
            <Typography variant="caption" display="block" color="text.secondary">
              Version 1.0.0
            </Typography>
          </CardContent>
        </Card>
      </Box>
    </Box>
  );

  return (
    <Box sx={{ display: 'flex', minHeight: '100vh' }}>
      {/* App Bar */}
      <AppBar
        position="fixed"
        sx={{
          width: { lg: `calc(100% - ${drawerWidth}px)` },
          ml: { lg: `${drawerWidth}px` },
          zIndex: theme.zIndex.drawer + 1,
          backgroundColor: 'primary.main',
          boxShadow: 1,
        }}
      >
        <Toolbar>
          <IconButton
            color="inherit"
            aria-label="open drawer"
            edge="start"
            onClick={handleDrawerToggle}
            sx={{ mr: 2, display: { lg: 'none' } }}
          >
            <MenuIcon />
          </IconButton>

          {/* Breadcrumbs */}
          <Box sx={{ flexGrow: 1, ml: { xs: 0, lg: 2 } }}>
            <Breadcrumbs
              aria-label="breadcrumb"
              sx={{
                '& .MuiBreadcrumbs-ol': {
                  color: 'white',
                },
                '& .MuiBreadcrumbs-separator': {
                  color: 'rgba(255, 255, 255, 0.7)',
                },
              }}
            >
              <Link
                color="inherit"
                onClick={() => handleNavigation('/dashboard')}
                sx={{
                  cursor: 'pointer',
                  '&:hover': { textDecoration: 'underline' },
                }}
              >
                <Home sx={{ mr: 0.5, fontSize: '1rem' }} />
                Dashboard
              </Link>
              {generateBreadcrumbs().map((crumb, index) => (
                <Typography key={index} color="inherit" sx={{ fontWeight: 500 }}>
                  {crumb.label}
                </Typography>
              ))}
            </Breadcrumbs>
          </Box>

          {/* Right side icons */}
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
            {/* Notifications */}
            <Tooltip title="Notifications">
              <IconButton color="inherit">
                <Badge badgeContent={notifications} color="error">
                  <Notifications />
                </Badge>
              </IconButton>
            </Tooltip>

            {/* User Profile */}
            <Tooltip title="Account settings">
              <IconButton onClick={handleProfileMenuOpen} color="inherit">
                <Avatar sx={{ width: 32, height: 32, bgcolor: 'secondary.main' }}>
                  <AccountCircle />
                </Avatar>
              </IconButton>
            </Tooltip>
          </Box>
        </Toolbar>
      </AppBar>

      {/* Profile Menu */}
      <Menu
        anchorEl={anchorEl}
        open={Boolean(anchorEl)}
        onClose={handleProfileMenuClose}
        onClick={handleProfileMenuClose}
        PaperProps={{
          elevation: 0,
          sx: {
            overflow: 'visible',
            filter: 'drop-shadow(0px 2px 8px rgba(0,0,0,0.32))',
            mt: 1.5,
            '& .MuiAvatar-root': {
              width: 32,
              height: 32,
              ml: -0.5,
              mr: 1,
            },
          },
        }}
        transformOrigin={{ horizontal: 'right', vertical: 'top' }}
        anchorOrigin={{ horizontal: 'right', vertical: 'bottom' }}
      >
        <MenuItem onClick={handleProfileMenuClose}>
          <Avatar sx={{ bgcolor: 'primary.main' }} /> Profile
        </MenuItem>
        <MenuItem onClick={handleProfileMenuClose}>
          <Avatar sx={{ bgcolor: 'secondary.main' }}>
            <Settings fontSize="small" />
          </Avatar>{' '}
          Settings
        </MenuItem>
        <Divider />
        <MenuItem onClick={handleProfileMenuClose}>
          <Avatar sx={{ bgcolor: 'error.main' }}>
            <Logout fontSize="small" />
          </Avatar>{' '}
          Logout
        </MenuItem>
      </Menu>

      {/* Navigation Drawer */}
      <Box component="nav" sx={{ width: { lg: drawerWidth }, flexShrink: { lg: 0 } }}>
        {/* Mobile Drawer */}
        <Drawer
          variant="temporary"
          open={mobileOpen}
          onClose={handleDrawerToggle}
          ModalProps={{
            keepMounted: true, // Better mobile performance
          }}
          sx={{
            display: { xs: 'block', lg: 'none' },
            '& .MuiDrawer-paper': {
              boxSizing: 'border-box',
              width: drawerWidth,
              borderRight: 1,
              borderColor: 'divider',
            },
          }}
        >
          {drawer}
        </Drawer>

        {/* Desktop Drawer */}
        <Drawer
          variant="permanent"
          sx={{
            display: { xs: 'none', lg: 'block' },
            '& .MuiDrawer-paper': {
              boxSizing: 'border-box',
              width: drawerWidth,
              borderRight: 1,
              borderColor: 'divider',
            },
          }}
          open
        >
          {drawer}
        </Drawer>
      </Box>

      {/* Main Content */}
      <Box
        component="main"
        sx={{
          flexGrow: 1,
          p: 3,
          width: { lg: `calc(100% - ${drawerWidth}px)` },
          minHeight: '100vh',
          backgroundColor: 'background.default',
        }}
      >
        <Toolbar /> {/* Spacer for AppBar */}
        {/* Page Content */}
        <Box sx={{ mt: 2 }}>
          <Outlet />
        </Box>
      </Box>
    </Box>
  );
};

export default AppTailwind;
