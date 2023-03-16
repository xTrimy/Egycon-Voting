/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {},
  }, 
  darkMode: 'class',
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
