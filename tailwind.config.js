/** @type {import("tailwindcss").Config} */
module.exports = {
    darkMode: "class",
    content: [
        "./templates/**/*.html.twig",
        "node_modules/preline/dist/*.js"
    ],
    theme: {
        extend: {
            fontFamily: {
                "sans": "Lato, sans-serif",
            }
        }
    },
    plugins: [
        require('@tailwindcss/forms'),
        require("preline/plugin")
    ]
};

