import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                // Inter: bersih & mudah dibaca di segala ukuran layar
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                // Latar hangat & ramah (cream)
                cream: {
                    50: '#fdfbf7',
                    100: '#f7f3ea',
                    200: '#efe8d8',
                    300: '#e2d6bd',
                },

                // Tinta hangat untuk teks (kontras tinggi, tidak terlalu hitam)
                ink: {
                    300: '#b5a89a',
                    400: '#8d8174',
                    500: '#6b6155',
                    600: '#57503f',
                    700: '#443d31',
                    800: '#332d25',
                    900: '#241f1a',
                },

                // Hijau hangat sebagai warna utama (brand)
                brand: {
                    50: '#eefaf2',
                    100: '#d7f2e3',
                    200: '#b3e5cb',
                    300: '#86d2ae',
                    400: '#52b98c',
                    500: '#339e73',
                    600: '#23815b',
                    700: '#1d684a',
                    800: '#18533b',
                    900: '#11402f',
                },
            },

            fontSize: {
                // Ukuran teks lebih besar untuk kenyamanan membaca
                '2xl': ['1.5rem', { lineHeight: '2rem' }],
                '3xl': ['1.75rem', { lineHeight: '2.25rem' }],
                '4xl': ['2.125rem', { lineHeight: '2.5rem' }],
            },
        },
    },

    plugins: [forms],
};
