/** @type {import("tailwindcss").Config} */
module.exports = {
    darkMode: "class",
    content: [
        "./templates/**/*.html.twig",
        "./vendor/symfony/twig-bridge/Resources/views/Form/tailwind_2_layout.html.twig",
        "node_modules/preline/dist/*.js"
    ],
    theme: {
        extend: {
            fontFamily: {
                "sans": "Inter, sans-serif",
            }
        }
    },
    plugins: [
        require('@tailwindcss/forms'),
        require("preline/plugin")
    ]
};
