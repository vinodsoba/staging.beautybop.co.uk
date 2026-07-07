/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    // Magento templates
    "./**/*.phtml",

    // Knockout templates
    "./Magento_*/web/template/**/*.html",

    // Your JavaScript only
    "./web/js/**/*.js",

    // Tailwind component HTML (if ever added)
    "./web/**/*.html"
  ],
  theme: {
    extend: {},
  },

  plugins: [],
}