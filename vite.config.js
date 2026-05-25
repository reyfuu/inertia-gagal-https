import path from 'path';
import { fileURLToPath } from 'url';
import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

/** Biarkan Vite melayani asset HMR; sisanya diteruskan ke Laravel (php artisan serve). */
function shouldServeWithVite(url) {
    const pathname = (url ?? '').split('?')[0];

    if (
        pathname.startsWith('/@') ||
        pathname.startsWith('/resources/') ||
        pathname.startsWith('/node_modules/') ||
        pathname.startsWith('/build/') ||
        pathname.startsWith('/_debugbar/') ||
        pathname === '/fonts-manifest.json'
    ) {
        return true;
    }

    return /\.(js|mjs|ts|jsx|tsx|css|scss|sass|less|json|map|woff2?|ttf|eot|svg|png|jpe?g|gif|webp|ico)$/i.test(pathname);
}

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const laravelUrl = env.VITE_LARAVEL_URL || 'http://127.0.0.1:8000';

    return {
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        react(),
        tailwindcss(),
    ],
    server: {
        host: 'localhost',
        port: 5174, // Changed from 5173 to 5174 to avoid conflict
        strictPort: true,
        open: '/',
        proxy: {
            '/': {
                target: laravelUrl,
                changeOrigin: true,
                bypass: (req) => (shouldServeWithVite(req.url) ? req.url : undefined),
            },
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
    };
});
