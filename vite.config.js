import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.jsx'], // 2. Change .js to .jsx
            refresh: true,
        }),
        react(), // 3. Add the React plugin here
    ],
});
