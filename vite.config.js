import {resolve} from 'path'

const root = "./assets";

const twigRefreshPlugin = () => ({
    name: 'twig-refresh',
    configureServer({watcher, ws}) {
        watcher.add(resolve(__dirname, "templates/**/*.twig"));
        watcher.on("change", function (path) {
            if (path.endsWith(".twig")) {
                ws.send({
                    type: 'full-reload',
                })
            }
        });
    }
});

const config = {
    emitManifest: true,
    cors: true,
    base: '/assets/',
    build: {
        polyfillDynamicImport: false,
        assetsDir: '',
        manifest: true,
        outDir: '../public/assets/',
        rollupOptions: {
            output: {
                manualChunks: undefined // Désactive la séparation du vendor
            },
            input: {
                // app: resolve(__dirname, 'assets/app.js'),
                admin: resolve(__dirname, 'assets/admin.js')
            }
        },
    },
    plugins: [twigRefreshPlugin()],
    root
};

module.exports = config;

