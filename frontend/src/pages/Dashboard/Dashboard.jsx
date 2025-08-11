import React, { useState, useEffect } from 'react';
import { 
  CubeIcon, 
  UserGroupIcon, 
  DocumentTextIcon, 
  CurrencyDollarIcon,
  ChartBarIcon,
  ArrowTrendingUpIcon,
  PlusIcon,
  EyeIcon
} from '@heroicons/react/24/outline';
import { 
  customersAPI, 
  suppliersAPI, 
  barangAPI, 
  invoicesAPI, 
  categoriesAPI 
} from '../../services/api';

const Dashboard = () => {
  const [stats, setStats] = useState({
    totalCustomers: 0,
    totalSuppliers: 0,
    totalProducts: 0,
    totalInvoices: 0,
    totalCategories: 0
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchDashboardData();
  }, []);

  const fetchDashboardData = async () => {
    try {
      setLoading(true);
      console.log('🔄 Fetching dashboard data from Laravel backend...');
      
      // Fetch data from multiple endpoints
      const [customers, suppliers, products, invoices, categories] = await Promise.all([
        customersAPI.getAll().catch((error) => {
          console.error('❌ Customers API Error:', error);
          return { data: { data: [] } };
        }),
        suppliersAPI.getAll().catch((error) => {
          console.error('❌ Suppliers API Error:', error);
          return { data: { data: [] } };
        }),
        barangAPI.getAll().catch((error) => {
          console.error('❌ Barang API Error:', error);
          return { data: { data: [] } };
        }),
        invoicesAPI.getAll().catch((error) => {
          console.error('❌ Invoices API Error:', error);
          return { data: { data: [] } };
        }),
        categoriesAPI.getAll().catch((error) => {
          console.error('❌ Categories API Error:', error);
          return { data: { data: [] } };
        })
      ]);

      console.log('📊 Laravel API Responses:', {
        customers: customers.data,
        suppliers: suppliers.data,
        products: products.data,
        invoices: invoices.data,
        categories: categories.data
      });

      // Laravel returns data in response.data.data format
      setStats({
        totalCustomers: customers.data?.data?.length || 0,
        totalSuppliers: suppliers.data?.data?.length || 0,
        totalProducts: products.data?.data?.length || 0,
        totalInvoices: invoices.data?.data?.length || 0,
        totalCategories: categories.data?.data?.length || 0
      });
    } catch (error) {
      console.error('❌ Error fetching dashboard data:', error);
    } finally {
      setLoading(false);
    }
  };

  const StatCard = ({ title, value, icon: Icon, color, trend, bgColor }) => (
    <div className={`${bgColor} rounded-2xl p-6 shadow-xl border border-white/20 hover:shadow-2xl transition-all duration-300 transform hover:scale-105`}>
      <div className="flex items-center justify-between">
        <div className="flex-1">
          <p className="text-sm font-semibold text-white/90 uppercase tracking-wide">{title}</p>
          <p className="text-4xl font-bold mt-3 text-white">
            {loading ? (
              <span className="animate-pulse">...</span>
            ) : (
              value.toLocaleString()
            )}
          </p>
          {trend && (
            <div className="flex items-center mt-3 text-sm text-white/80">
              <ArrowTrendingUpIcon className="w-4 h-4 mr-2" />
              <span className="font-medium">{trend}</span>
            </div>
          )}
        </div>
        <div className="p-4 bg-white/20 rounded-2xl backdrop-blur-sm">
          <Icon className="w-10 h-10 text-white" />
        </div>
      </div>
    </div>
  );

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 p-6">
      {/* Header */}
      <div className="mb-8">
        <h1 className="text-4xl font-bold text-white mb-3 bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
          Dashboard
        </h1>
        <p className="text-lg text-white/70">Selamat datang di StockFlow Management System</p>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <StatCard
          title="Total Customers"
          value={stats.totalCustomers}
          icon={UserGroupIcon}
          bgColor="bg-gradient-to-br from-blue-600 to-blue-700"
          trend="+12% from last month"
        />
        
        <StatCard
          title="Total Suppliers"
          value={stats.totalSuppliers}
          icon={UserGroupIcon}
          bgColor="bg-gradient-to-br from-emerald-600 to-emerald-700"
          trend="+5% from last month"
        />
        
        <StatCard
          title="Total Products"
          value={stats.totalProducts}
          icon={CubeIcon}
          bgColor="bg-gradient-to-br from-purple-600 to-purple-700"
          trend="+8% from last month"
        />
        
        <StatCard
          title="Total Invoices"
          value={stats.totalInvoices}
          icon={DocumentTextIcon}
          bgColor="bg-gradient-to-br from-orange-600 to-orange-700"
          trend="+15% from last month"
        />
        
        <StatCard
          title="Categories"
          value={stats.totalCategories}
          icon={ChartBarIcon}
          bgColor="bg-gradient-to-br from-pink-600 to-pink-700"
          trend="+3% from last month"
        />
      </div>

      {/* Content Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Quick Actions */}
        <div className="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl border border-white/20 p-6">
          <h2 className="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <PlusIcon className="w-6 h-6 mr-2 text-blue-600" />
            Quick Actions
          </h2>
          <div className="space-y-4">
            <button className="w-full text-left p-4 rounded-xl bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-200 hover:from-blue-100 hover:to-blue-200 hover:border-blue-300 transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
              <div className="flex items-center">
                <div className="p-2 bg-blue-600 rounded-lg mr-4">
                  <CubeIcon className="w-6 h-6 text-white" />
                </div>
                <div>
                  <span className="font-bold text-gray-900 text-lg">Add New Product</span>
                  <p className="text-sm text-gray-600 mt-1">Create new product entry</p>
                </div>
              </div>
            </button>
            
            <button className="w-full text-left p-4 rounded-xl bg-gradient-to-r from-emerald-50 to-emerald-100 border-2 border-emerald-200 hover:from-emerald-100 hover:to-emerald-200 hover:border-emerald-300 transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
              <div className="flex items-center">
                <div className="p-2 bg-emerald-600 rounded-lg mr-4">
                  <UserGroupIcon className="w-6 h-6 text-white" />
                </div>
                <div>
                  <span className="font-bold text-gray-900 text-lg">Add New Customer</span>
                  <p className="text-sm text-gray-600 mt-1">Register new customer</p>
                </div>
              </div>
            </button>
            
            <button className="w-full text-left p-4 rounded-xl bg-gradient-to-r from-purple-50 to-purple-100 border-2 border-purple-200 hover:from-purple-100 hover:to-purple-200 hover:border-purple-300 transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
              <div className="flex items-center">
                <div className="p-2 bg-purple-600 rounded-lg mr-4">
                  <DocumentTextIcon className="w-6 h-6 text-white" />
                </div>
                <div>
                  <span className="font-bold text-gray-900 text-lg">Create Invoice</span>
                  <p className="text-sm text-gray-600 mt-1">Generate new invoice</p>
                </div>
              </div>
            </button>
            
            <button className="w-full text-left p-4 rounded-xl bg-gradient-to-r from-orange-50 to-orange-100 border-2 border-orange-200 hover:from-orange-100 hover:to-orange-200 hover:border-orange-300 transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
              <div className="flex items-center">
                <div className="p-2 bg-orange-600 rounded-lg mr-4">
                  <ChartBarIcon className="w-6 h-6 text-white" />
                </div>
                <div>
                  <span className="font-bold text-gray-900 text-lg">View Reports</span>
                  <p className="text-sm text-gray-600 mt-1">Access analytics & reports</p>
                </div>
              </div>
            </button>
          </div>
        </div>

        {/* Recent Activity */}
        <div className="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl border border-white/20 p-6">
          <h2 className="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <EyeIcon className="w-6 h-6 mr-2 text-purple-600" />
            Recent Activity
          </h2>
          <div className="space-y-4">
            <div className="flex items-start p-3 rounded-lg bg-emerald-50 border border-emerald-200">
              <div className="w-3 h-3 bg-emerald-500 rounded-full mt-2 mr-4 shadow-sm"></div>
              <div className="flex-1">
                <p className="text-sm font-bold text-gray-900">New customer added</p>
                <p className="text-xs text-gray-600 mt-1">2 hours ago</p>
              </div>
            </div>
            
            <div className="flex items-start p-3 rounded-lg bg-blue-50 border border-blue-200">
              <div className="w-3 h-3 bg-blue-500 rounded-full mt-2 mr-4 shadow-sm"></div>
              <div className="flex-1">
                <p className="text-sm font-bold text-gray-900">Invoice #INV-001 created</p>
                <p className="text-xs text-gray-600 mt-1">4 hours ago</p>
              </div>
            </div>
            
            <div className="flex items-start p-3 rounded-lg bg-purple-50 border border-purple-200">
              <div className="w-3 h-3 bg-purple-500 rounded-full mt-2 mr-4 shadow-sm"></div>
              <div className="flex-1">
                <p className="text-sm font-bold text-gray-900">Stock updated for 15 items</p>
                <p className="text-xs text-gray-600 mt-1">6 hours ago</p>
              </div>
            </div>
            
            <div className="flex items-start p-3 rounded-lg bg-orange-50 border border-orange-200">
              <div className="w-3 h-3 bg-orange-500 rounded-full mt-2 mr-4 shadow-sm"></div>
              <div className="flex-1">
                <p className="text-sm font-bold text-gray-900">Monthly report generated</p>
                <p className="text-xs text-gray-600 mt-1">1 day ago</p>
              </div>
            </div>
          </div>
        </div>

        {/* System Status */}
        <div className="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl border border-white/20 p-6">
          <h2 className="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <ChartBarIcon className="w-6 h-6 mr-2 text-indigo-600" />
            System Status
          </h2>
          <div className="space-y-4">
            <div className="flex items-center justify-between p-3 rounded-lg bg-emerald-50 border border-emerald-200">
              <span className="text-sm font-semibold text-gray-700">API Status</span>
              <span className="px-3 py-1 bg-emerald-500 text-white text-xs font-bold rounded-full shadow-sm">
                Online
              </span>
            </div>
            
            <div className="flex items-center justify-between p-3 rounded-lg bg-emerald-50 border border-emerald-200">
              <span className="text-sm font-semibold text-gray-700">Database</span>
              <span className="px-3 py-1 bg-emerald-500 text-white text-xs font-bold rounded-full shadow-sm">
                Connected
              </span>
            </div>
            
            <div className="flex items-center justify-between p-3 rounded-lg bg-blue-50 border border-blue-200">
              <span className="text-sm font-semibold text-gray-700">Backup Status</span>
              <span className="px-3 py-1 bg-blue-500 text-white text-xs font-bold rounded-full shadow-sm">
                Up to date
              </span>
            </div>
            
            <div className="flex items-center justify-between p-3 rounded-lg bg-yellow-50 border border-yellow-200">
              <span className="text-sm font-semibold text-gray-700">Server Load</span>
              <span className="px-3 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full shadow-sm">
                Normal
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;
