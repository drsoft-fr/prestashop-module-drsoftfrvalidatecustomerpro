module.exports = [
  {
    extends: [
      'eslint:recommended',
      'plugin:import/recommended',
      'plugin:jsx-a11y/recommended',
      'plugin:@typescript-eslint/recommended',
      'eslint-config-prettier',
    ],
    globals: {
      process: true,
    },
    settings: {
      'import/resolver': {
        node: {
          paths: ['src'],
          extensions: ['.js', '.jsx', '.ts', '.tsx'],
        },
        typescript: {},
      },
    },
    ignores: ['admin-dev/', 'node_modules/', ',.prettierrc'],
  },
]
