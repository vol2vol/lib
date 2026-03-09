import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

export default defineConfig({
  plugins: [react()],
  resolve: {
    alias: {
      '@app': path.resolve(__dirname, 'src/app'),
      '@pages': path.resolve(__dirname, 'src/pages'),
      '@components': path.resolve(__dirname, 'src/components'),
      '@types': path.resolve(__dirname, 'src/types'),
      '@data': path.resolve(__dirname, 'src/data'),
      '@assets': path.resolve(__dirname, 'src/assets'),
      '@api': path.resolve(__dirname, 'src/api'),
    },
  },
})
