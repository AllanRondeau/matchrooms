/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
          colors: {
            matchroom: {
              dark: '#2E2E38', 
              pink: '#FF135F',
              navy: '#0E2748',
              cream: '#E9E1DC',
              peach: '#F7E6E2',
              blue: '#D6E1E5',
              gray: '#F4F3F3',
            },
          },
          fontFamily: {
            inter: ['Inter', 'sans-serif'],
            righteous: ['Righteous', 'cursive'],
            playfair: ['Playfair Display', 'serif'],
          },
        },
      },
      plugins: [],
    }
