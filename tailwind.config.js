// tailwind.config.js

module.exports = {
    // Liste des fichiers où Tailwind doit scanner les classes (très important !)
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {        
        extend: {
            // Vos personnalisations
            backgroundImage: {
                'wtt-gradient': 'linear-gradient(90deg, #22aaec, #a32ff0)',
            },
            colors: {
                'wtt-blue': '#22aaec',
                'wtt-purple': '#a32ff0',
                'vert': '#C2F6B8',
                'premier': '#0600d5',
                'deuxieme': '#00c3ff',
                'troisieme': '#00c3ff',
            },
            fontFamily: {
                // S'assure que 'cherry' est disponible pour les classes font-cherry
                cherry: ['Cherry Bomb One'], 
            },
        },
    },
    // Les plugins de Tailwind (pas les plugins Vite !)
    plugins: [],
}