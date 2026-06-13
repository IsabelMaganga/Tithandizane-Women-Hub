/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./App.tsx",
    "./components/**/*.{js,jsx,ts,tsx}",
    "./app/**/*.{js,ts,jsx,tsx}",
  ],
  // 'class' lets NativeWind's setColorScheme() control dark mode programmatically.
  // 'media' (the default) ties dark mode to the OS and blocks manual toggling on web.
  darkMode: 'class',
  presets: [require("nativewind/preset")],
  theme: {
    extend: {},
  },
  plugins: [],
};