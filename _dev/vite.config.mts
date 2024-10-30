import eslint from 'vite-plugin-eslint'
import { resolve } from 'path'
import { defineConfig } from 'vite'

const psRootDir = resolve(__dirname, '../../../admin-dev/themes/new-theme')
const psJsDir = resolve(psRootDir, './js')

export default defineConfig({
  build: {
    manifest: true,
    rollupOptions: {
      external: ['prestashop', '$', 'jquery', 'vue'],
      input: {
        'admin-app': resolve(__dirname, 'src/js/admin/bootstrap.ts'),
      },
      output: {
        assetFileNames: (assetInfo) => {
          const info = assetInfo.name.split('.')
          let extType = info[info.length - 1].toLowerCase()

          if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(extType)) {
            extType = 'img'
          } else if (/woff|woff2/.test(extType)) {
            extType = 'css'
          }

          return `${extType}/[name]-[hash][extname]`
        },
        chunkFileNames: 'js/[name]-[hash].js',
        entryFileNames: 'js/[name]-[hash].js',
        globals: {
          prestashop: 'prestashop',
          $: '$',
          jquery: 'jQuery',
          vue: 'vue',
        },
      },
    },
    outDir: '../views',
  },
  plugins: [eslint()],
  resolve: {
    alias: {
      '@app': resolve(psJsDir, './app'),
      '@components': resolve(psJsDir, './components'),
      '@js': resolve(psJsDir),
      '@src': resolve(__dirname, './src'),
      '@pages': resolve(psJsDir, './pages'),
      '@node_modules': resolve(psRootDir, './node_modules'),
      '@PSVue': resolve(psJsDir, './vue'),
      '@PSTypes': resolve(psJsDir, './types'),
      '@vue': resolve(psJsDir, './vue'),
    },
  },
})
