/** @type {import('tailwindcss').Config} */
import daisyui from 'daisyui'

export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        'text': '#fafafa',
        'background': '#0f0f0f',
        'background-200': '#505050',
        'background-500': '#7c7c7c',
        'surface': '#464646',
        'on-surface': '#726954',
        'primary': '#ffe44f',
        'secondary': '#f2af56',
        'accent': '#9c7321',
        'accent-200': 'rgba(156,115,33,0.2)',
        'accent-500': 'rgba(156,115,33,0.5)',
        'accent-800': 'rgba(156,115,33,0.8)',
        'danger': '#e06666',
        'success': '#00ff00',
        'info': '#0000ff',
        'warning': '#ffff00',
      },
      fontSize: {
        sm: '0.750rem',
        base: '1rem',
        xl: '1.333rem',
        '2xl': '1.777rem',
        '3xl': '2.369rem',
        '4xl': '3.158rem',
        '5xl': '4.210rem',
      },
      fontFamily: {
        heading: 'Climate Crisis',
        body: 'Lexend',
      },
      fontWeight: {
        normal: '400',
        bold: '700',
      },
    },
  },
  plugins: [
    daisyui,
  ],
  daisyui: {
    themes: [
      {
        mytheme: {
          "primary": "#ffe44f",
          "secondary": "#f2af56",
          "accent": "#9c7321",
          "neutral": "#464646",
          "base-100": "#949494",
          "base-200": "#0f0f0f",
          "base-300": "#505050",
          "base-content": "#4b4b4b",
        },
      },
    ],
  },
}