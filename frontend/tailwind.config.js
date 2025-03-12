/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./components/**/*.{js,vue,ts}",
    "./layouts/**/*.vue",
    "./pages/**/*.vue",
    "./plugins/**/*.{js,ts}",
    "./app.vue",
    "./error.vue"
  ],
  theme: {
    extend: {
      colors: {
        'navy': {
          600: '#0078C8',
          700: '#0067B0',
          800: '#005697',
          900: '#051D40',
        },
        'blue': {
          400: '#00A0E4',
          500: '#0090CD',
          600: '#0078C8',
          700: '#0067B0',
          800: '#005697',
          900: '#051D40',
        },
      },
    },
  },
  plugins: [],
}