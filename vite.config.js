import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                'resources/css/filament/admin/theme.css',
                "public/css/filament/filament/app.css",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
