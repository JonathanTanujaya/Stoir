import React, { useState } from 'react';
import { Outlet } from 'react-router-dom';
import Sidebar from './components/Layout/Sidebar';
import MainContent from './components/Layout/MainContent';

function AppTailwind() {
  const [sidebarCollapsed, setSidebarCollapsed] = useState(false);

  return (
    <div className="min-h-screen bg-gradient-brand">
      <div className="flex h-screen">
        {/* Sidebar */}
        <Sidebar 
          collapsed={sidebarCollapsed} 
          onToggle={() => setSidebarCollapsed(!sidebarCollapsed)} 
        />
        
        {/* Main Content */}
        <div className={`flex-1 transition-all duration-300 ${sidebarCollapsed ? 'ml-16' : 'ml-72'}`}>
          <MainContent>
            <Outlet />
          </MainContent>
        </div>
      </div>
    </div>
  );
}

export default AppTailwind;
