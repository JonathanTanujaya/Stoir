import React, { useState } from 'react';
import { useAuth } from '../../contexts/AuthContext';
import { useNavigate, Outlet, Link } from 'react-router-dom';
import {
  AppBar,
  Toolbar,
  Typography,
  IconButton,
  Drawer,
  List,
  ListItem,
  ListItemIcon,
  ListItemText,
  Box,
  Avatar,
  Menu,
  MenuItem,
  Divider,
  useTheme,
  useMediaQuery,
  Collapse
} from '@mui/material';
import {
  Menu as MenuIcon,
  AccountCircle,
  Logout,
  Dashboard,
  People,
  Inventory,
  Receipt,
  Store,
  Category,
  LocationOn,
  Business,
  AccountBalance,
  Description,
  Settings,
  ExpandLess,
  ExpandMore
} from '@mui/icons-material';

const drawerWidth = 280;

const MainLayout = () => {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const theme = useTheme();
  const isMobile = useMediaQuery(theme.breakpoints.down('md'));
  
  const [mobileOpen, setMobileOpen] = useState(false);
  const [anchorEl, setAnchorEl] = useState(null);
  const [expandedMenus, setExpandedMenus] = useState({});

  const handleDrawerToggle = () => {
    setMobileOpen(!mobileOpen);
  };

  const handleProfileMenuOpen = (event) => {
    setAnchorEl(event.currentTarget);
  };

  const handleProfileMenuClose = () => {
    setAnchorEl(null);
  };

  const handleLogout = async () => {
    handleProfileMenuClose();
    await logout();
    navigate('/login');
  };

  const handleExpandMenu = (menuName) => {
    setExpandedMenus(prev => ({
      ...prev,
      [menuName]: !prev[menuName]
    }));
  };

  const menuItems = [
    {
      title: 'Dashboard',
      icon: <Dashboard />,
      path: '/dashboard'
    },
    {
      title: 'Master Data',
      icon: <Settings />,
      children: [
        { title: 'Pelanggan', icon: <People />, path: '/customers' },
        { title: 'Supplier', icon: <Business />, path: '/suppliers' },
        { title: 'Sales', icon: <People />, path: '/sales' },
        { title: 'Barang', icon: <Inventory />, path: '/barang' },
        { title: 'Kategori', icon: <Category />, path: '/kategori' },
        { title: 'Area', icon: <LocationOn />, path: '/areas' },
        { title: 'Divisi', icon: <Store />, path: '/divisi' },
        { title: 'User', icon: <AccountCircle />, path: '/master-user' }
      ]
    },
    {
      title: 'Transaksi',
      icon: <Receipt />,
      children: [
        { title: 'Invoice', icon: <Receipt />, path: '/invoices' },
        { title: 'Part Penerimaan', icon: <Inventory />, path: '/part-penerimaan' },
        { title: 'Return Sales', icon: <Receipt />, path: '/return-sales' },
        { title: 'Claim Penjualan', icon: <Description />, path: '/claim-penjualan' },
        { title: 'Opname', icon: <Inventory />, path: '/opname' }
      ]
    },
    {
      title: 'Keuangan',
      icon: <AccountBalance />,
      children: [
        { title: 'Journal', icon: <Description />, path: '/journals' },
        { title: 'COA', icon: <AccountBalance />, path: '/mcoa' },
        { title: 'Bank', icon: <AccountBalance />, path: '/mbank' },
        { title: 'Saldo Bank', icon: <AccountBalance />, path: '/saldo-bank' }
      ]
    }
  ];

  const renderMenuItem = (item, index) => {
    if (item.children) {
      const isExpanded = expandedMenus[item.title];
      return (
        <React.Fragment key={item.title}>
          <ListItem 
            button 
            onClick={() => handleExpandMenu(item.title)}
            sx={{ pl: 2 }}
          >
            <ListItemIcon>{item.icon}</ListItemIcon>
            <ListItemText primary={item.title} />
            {isExpanded ? <ExpandLess /> : <ExpandMore />}
          </ListItem>
          <Collapse in={isExpanded} timeout="auto" unmountOnExit>
            <List component="div" disablePadding>
              {item.children.map((child) => (
                <ListItem
                  key={child.path}
                  button
                  component={Link}
                  to={child.path}
                  sx={{ pl: 4 }}
                  onClick={isMobile ? handleDrawerToggle : undefined}
                >
                  <ListItemIcon>{child.icon}</ListItemIcon>
                  <ListItemText primary={child.title} />
                </ListItem>
              ))}
            </List>
          </Collapse>
        </React.Fragment>
      );
    }

    return (
      <ListItem
        key={item.path}
        button
        component={Link}
        to={item.path}
        sx={{ pl: 2 }}
        onClick={isMobile ? handleDrawerToggle : undefined}
      >
        <ListItemIcon>{item.icon}</ListItemIcon>
        <ListItemText primary={item.title} />
      </ListItem>
    );
  };

  const drawer = (
    <div>
      <Toolbar>
        <Typography variant="h6" noWrap component="div">
          STOIR System
        </Typography>
      </Toolbar>
      <Divider />
      <List>
        {menuItems.map(renderMenuItem)}
      </List>
    </div>
  );

  return (
    <Box sx={{ display: 'flex' }}>
      <AppBar
        position="fixed"
        sx={{
          width: { md: `calc(100% - ${drawerWidth}px)` },
          ml: { md: `${drawerWidth}px` },
        }}
      >
        <Toolbar>
          <IconButton
            color="inherit"
            aria-label="open drawer"
            edge="start"
            onClick={handleDrawerToggle}
            sx={{ mr: 2, display: { md: 'none' } }}
          >
            <MenuIcon />
          </IconButton>
          <Typography variant="h6" noWrap component="div" sx={{ flexGrow: 1 }}>
            Sistem Manajemen STOIR
          </Typography>
          <Box sx={{ display: 'flex', alignItems: 'center' }}>
            <Typography variant="body2" sx={{ mr: 2 }}>
              {user?.nama || user?.username}
            </Typography>
            <IconButton
              size="large"
              aria-label="account menu"
              aria-controls="profile-menu"
              aria-haspopup="true"
              onClick={handleProfileMenuOpen}
              color="inherit"
            >
              <Avatar sx={{ width: 32, height: 32 }}>
                {(user?.nama || user?.username || 'U').charAt(0).toUpperCase()}
              </Avatar>
            </IconButton>
          </Box>
        </Toolbar>
      </AppBar>

      <Menu
        id="profile-menu"
        anchorEl={anchorEl}
        open={Boolean(anchorEl)}
        onClose={handleProfileMenuClose}
        onClick={handleProfileMenuClose}
      >
        <MenuItem>
          <ListItemIcon>
            <AccountCircle fontSize="small" />
          </ListItemIcon>
          Profile
        </MenuItem>
        <Divider />
        <MenuItem onClick={handleLogout}>
          <ListItemIcon>
            <Logout fontSize="small" />
          </ListItemIcon>
          Logout
        </MenuItem>
      </Menu>

      <Box
        component="nav"
        sx={{ width: { md: drawerWidth }, flexShrink: { md: 0 } }}
      >
        <Drawer
          variant="temporary"
          open={mobileOpen}
          onClose={handleDrawerToggle}
          ModalProps={{
            keepMounted: true, // Better open performance on mobile.
          }}
          sx={{
            display: { xs: 'block', md: 'none' },
            '& .MuiDrawer-paper': { boxSizing: 'border-box', width: drawerWidth },
          }}
        >
          {drawer}
        </Drawer>
        <Drawer
          variant="permanent"
          sx={{
            display: { xs: 'none', md: 'block' },
            '& .MuiDrawer-paper': { boxSizing: 'border-box', width: drawerWidth },
          }}
          open
        >
          {drawer}
        </Drawer>
      </Box>

      <Box
        component="main"
        sx={{
          flexGrow: 1,
          p: 3,
          width: { md: `calc(100% - ${drawerWidth}px)` }
        }}
      >
        <Toolbar />
        <Outlet />
      </Box>
    </Box>
  );
};

export default MainLayout;
