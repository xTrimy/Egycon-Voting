/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        "egycon-magenta": "#cb3398",
    },
  }, 
  darkMode: 'class',
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
}
