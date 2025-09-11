import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  base: '/',                      // 👈 important for subfolder
  plugins: [
    laravel({
      input: ['resources/js/app.js', 'resources/css/app.css'],
      refresh: true,
    }),
    vue(),                             // 👈 enable Vue SFC support
    tailwindcss(),                     // 👈 add Tailwind CSS plugin
  ],
})
