module.exports = {
  root: true,
  env: {
    browser: true,
    es2020: true,
    node: true,
  },
  extends: [
    'eslint:recommended',
    '@eslint/js/recommended',
    'plugin:react/recommended',
    'plugin:react/jsx-runtime',
    'plugin:react-hooks/recommended',
  ],
  ignorePatterns: ['dist', '.eslintrc.js'],
  parserOptions: {
    ecmaVersion: 'latest',
    sourceType: 'module',
    ecmaFeatures: {
      jsx: true,
    },
  },
  settings: {
    react: {
      version: '18.2',
    },
  },
  plugins: ['react-refresh'],
  rules: {
    'react/jsx-no-target-blank': 'off',
    'react-refresh/only-export-components': [
      'warn',
      { allowConstantExport: true },
    ],
    // Relax unused vars rule for development
    'no-unused-vars': ['warn', { 
      'argsIgnorePattern': '^_|^index$|^error$|^data$',
      'varsIgnorePattern': '^_|^useResponsive$|^setNotifications$|^useMemo$|^useState$|^useEffect$|^useCallback$|^process$',
      'ignoreRestSiblings': true
    }],
    // Allow console for debugging
    'no-console': 'warn',
    // Allow process.env
    'no-undef': ['error', { 'typeof': true }],
    // Relax some strict rules for development
    'react-hooks/exhaustive-deps': 'warn',
    'no-case-declarations': 'warn',
    'no-useless-catch': 'warn',
    'no-prototype-builtins': 'warn',
    'no-useless-escape': 'warn',
    'no-constant-binary-expression': 'warn',
  },
  globals: {
    process: 'readonly',
    global: 'readonly',
    __dirname: 'readonly',
    __filename: 'readonly',
    module: 'readonly',
    require: 'readonly',
    exports: 'readonly',
  },
}
