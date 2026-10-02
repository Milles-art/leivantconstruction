module.exports = {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './public/js/**/*.js',
        './storage/framework/views/*.php',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    safelist: [
        'bg-vant-blue',
        'bg-vant-green',
        'bg-vant-orange',
        'bg-vant-gold',
        'bg-vant-concrete',
        'text-black',
        'text-white',
    ],
    theme: {
        extend: {
            colors: {
                vant: {
                    black: '#0a0a0a',
                    graphite: '#111827',
                    card: '#1a1a1a',
                    steel: '#243447',
                    concrete: '#3f454d',
                    gold: '#D4A017',
                    orange: '#E87722',
                    green: '#2F855A',
                    blue: '#2563EB',
                    line: '#2b2b2b',
                },
            },
            fontFamily: {
                sans: ['Barlow', 'Arial', 'Helvetica', 'sans-serif'],
                heading: ['Barlow Condensed', 'Arial Narrow', 'Arial', 'Helvetica', 'sans-serif'],
            },
            boxShadow: {
                gold: '0 0 0 1px rgba(212, 160, 23, 0.25), 0 20px 60px rgba(0, 0, 0, 0.45)',
            },
        },
    },
    plugins: [],
};
