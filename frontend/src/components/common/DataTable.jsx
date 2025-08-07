import React from 'react';
import { LoadingSpinner, LoadingButton, EmptyState } from './LoadingComponents.jsx';

/**
 * Generic Data Table Component
 * Komponen table yang dapat digunakan ulang untuk semua list data
 */
const DataTable = ({
  title,
  data = [],
  columns = [],
  loading = false,
  error = null,
  onRefresh,
  onEdit,
  onDelete,
  keyExtractor,
  renderActions,
  emptyMessage = "Belum ada data",
  operationLoading = false
}) => {
  if (loading) {
    return <LoadingSpinner message={`Memuat data ${title.toLowerCase()}...`} />;
  }

  if (error) {
    return (
      <div style={{ padding: '2rem', textAlign: 'center', color: '#e74c3c' }}>
        <div>❌ {error}</div>
        <LoadingButton 
          onClick={onRefresh} 
          variant="primary" 
          style={{ marginTop: '1rem' }}
        >
          Coba Lagi
        </LoadingButton>
      </div>
    );
  }

  if (!data.length) {
    return (
      <EmptyState 
        message={emptyMessage}
        action={
          <LoadingButton onClick={onRefresh} variant="primary">
            Refresh
          </LoadingButton>
        }
      />
    );
  }

  return (
    <div style={{ padding: '1rem' }}>
      {/* Header */}
      <div style={{ 
        display: 'flex', 
        justifyContent: 'space-between', 
        alignItems: 'center', 
        marginBottom: '1rem' 
      }}>
        <h2 style={{ margin: 0 }}>{title} ({data.length})</h2>
        <LoadingButton onClick={onRefresh} variant="secondary" loading={loading}>
          Refresh
        </LoadingButton>
      </div>

      {/* Table */}
      <div style={{ overflowX: 'auto' }}>
        <table style={tableStyle}>
          <thead style={{ backgroundColor: '#f8f9fa' }}>
            <tr>
              {columns.map((column, index) => (
                <th key={index} style={tableHeaderStyle}>
                  {column.header}
                </th>
              ))}
              <th style={tableHeaderStyle}>Aksi</th>
            </tr>
          </thead>
          <tbody>
            {data.map((item, index) => {
              const key = keyExtractor ? keyExtractor(item, index) : `item-${index}`;
              const isValidData = keyExtractor ? keyExtractor(item, index) !== `item-${index}` : true;
              
              return (
                <tr key={key} style={tableRowStyle}>
                  {columns.map((column, colIndex) => (
                    <td key={colIndex} style={tableCellStyle}>
                      {column.render ? column.render(item) : (item[column.key] || '-')}
                    </td>
                  ))}
                  <td style={tableCellStyle}>
                    {renderActions ? renderActions(item, isValidData, operationLoading) : (
                      <div style={{ display: 'flex', gap: '0.5rem' }}>
                        <LoadingButton
                          onClick={() => onEdit && onEdit(item)}
                          disabled={!isValidData}
                          variant="primary"
                          style={actionButtonStyle}
                        >
                          Edit
                        </LoadingButton>
                        <LoadingButton
                          onClick={() => onDelete && onDelete(item)}
                          disabled={!isValidData || operationLoading}
                          loading={operationLoading}
                          variant="danger"
                          style={actionButtonStyle}
                        >
                          Hapus
                        </LoadingButton>
                      </div>
                    )}
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    </div>
  );
};

// Styles
const tableStyle = {
  width: '100%', 
  borderCollapse: 'collapse',
  backgroundColor: 'white',
  boxShadow: '0 2px 4px rgba(0,0,0,0.1)',
  borderRadius: '8px',
  overflow: 'hidden'
};

const tableHeaderStyle = {
  padding: '1rem 0.75rem',
  textAlign: 'left',
  fontWeight: '600',
  color: '#2c3e50',
  borderBottom: '2px solid #e9ecef'
};

const tableRowStyle = {
  borderBottom: '1px solid #e9ecef',
  transition: 'background-color 0.2s ease'
};

const tableCellStyle = {
  padding: '0.75rem',
  verticalAlign: 'middle'
};

const actionButtonStyle = {
  fontSize: '0.8rem', 
  padding: '0.25rem 0.5rem'
};

/**
 * Status Badge Component
 */
export const StatusBadge = ({ active, activeText = 'Aktif', inactiveText = 'Tidak Aktif' }) => (
  <span style={{
    padding: '0.25rem 0.5rem',
    borderRadius: '4px',
    fontSize: '0.8rem',
    backgroundColor: active ? '#27ae60' : '#e74c3c',
    color: 'white'
  }}>
    {active ? activeText : inactiveText}
  </span>
);

/**
 * Currency formatter
 */
export const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID').format(amount || 0);
};

export default DataTable;
