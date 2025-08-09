import React from 'react';

const MainContent = ({ children }) => {
  return (
    <div className="p-5 h-full overflow-auto">
      <div className="card min-h-full">
        {children}
      </div>
    </div>
  );
};

export default MainContent;
