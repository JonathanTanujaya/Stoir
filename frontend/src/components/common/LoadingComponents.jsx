import React from 'react';

/**
 * Loading Spinner sederhana
 */
export const LoadingSpinner = ({ message = 'Memuat data...' }) => (
  <div style={{ 
    display: 'flex', 
    justifyContent: 'center', 
    alignItems: 'center', 
    padding: '2rem',
    flexDirection: 'column'
  }}>
    <div style={{
      border: '3px solid #f3f3f3',
      borderTop: '3px solid #3498db',
      borderRadius: '50%',
      width: '30px',
      height: '30px',
      animation: 'spin 1s linear infinite',
      marginBottom: '1rem'
    }}></div>
    <div>{message}</div>
    <style jsx>{`
      @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }
    `}</style>
  </div>
);

/**
 * Loading Button dengan state
 */
export const LoadingButton = ({ 
  children, 
  loading = false, 
  disabled = false, 
  onClick,
  variant = 'primary',
  ...props 
}) => {
  const getButtonStyle = () => {
    const baseStyle = {
      padding: '0.5rem 1rem',
      border: 'none',
      borderRadius: '4px',
      cursor: loading || disabled ? 'not-allowed' : 'pointer',
      fontSize: '0.9rem',
      transition: 'all 0.2s ease',
      opacity: loading || disabled ? 0.6 : 1,
      display: 'inline-flex',
      alignItems: 'center',
      gap: '0.5rem'
    };

    const variants = {
      primary: {
        backgroundColor: '#3498db',
        color: 'white'
      },
      danger: {
        backgroundColor: '#e74c3c',
        color: 'white'
      },
      success: {
        backgroundColor: '#27ae60',
        color: 'white'
      },
      secondary: {
        backgroundColor: '#95a5a6',
        color: 'white'
      }
    };

    return { ...baseStyle, ...variants[variant] };
  };

  return (
    <button
      style={getButtonStyle()}
      disabled={loading || disabled}
      onClick={loading ? undefined : onClick}
      {...props}
    >
      {loading && (
        <div style={{
          width: '12px',
          height: '12px',
          border: '2px solid transparent',
          borderTop: '2px solid currentColor',
          borderRadius: '50%',
          animation: 'spin 1s linear infinite'
        }}></div>
      )}
      {loading ? 'Loading...' : children}
    </button>
  );
};

/**
 * Confirmation Dialog Hook
 */
export const useConfirmDialog = () => {
  const confirm = (message = 'Apakah Anda yakin?') => {
    return window.confirm(message);
  };

  const confirmDelete = (itemName = 'data ini') => {
    return window.confirm(`Apakah Anda yakin ingin menghapus ${itemName}? Tindakan ini tidak dapat dibatalkan.`);
  };

  return { confirm, confirmDelete };
};

/**
 * Error State Component
 */
export const ErrorState = ({ message = 'Terjadi kesalahan', onRetry }) => (
  <div style={{
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    padding: '2rem',
    color: '#e74c3c'
  }}>
    <div style={{ fontSize: '1.2rem', marginBottom: '1rem' }}>⚠️ {message}</div>
    {onRetry && (
      <LoadingButton onClick={onRetry} variant="primary">
        Coba Lagi
      </LoadingButton>
    )}
  </div>
);

/**
 * Empty State Component
 */
export const EmptyState = ({ message = 'Tidak ada data', action }) => (
  <div style={{
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    padding: '3rem',
    color: '#7f8c8d'
  }}>
    <div style={{ fontSize: '3rem', marginBottom: '1rem' }}>📄</div>
    <div style={{ fontSize: '1.1rem', marginBottom: '1rem' }}>{message}</div>
    {action}
  </div>
);

export default { LoadingSpinner, LoadingButton, useConfirmDialog, ErrorState, EmptyState };
