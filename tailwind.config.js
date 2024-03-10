const defaultTheme = require('tailwindcss/defaultTheme')

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./views/**/*.blade.php",
    "./vendor/s4mpp/element/views/**/*.blade.php",
  ],
  theme: {
  },
  plugins: [
    require('@tailwindcss/forms'),
],
}