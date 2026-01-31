module.exports = {
    pwa: {
        name: "Tocare Music",
        themeColor: "#4DBA87",
        msTileColor: "#000000",
        appleMobileWebAppCapable: "yes",
        appleMobileWebAppStatusBarStyle: "black",
        workboxPluginMode: "GenerateSW",
        workboxOptions: {
            runtimeCaching: [
                {
                    urlPattern: /^https:\/\/meusite\.com\/api\/musicas/,
                    handler: "CacheFirst",
                    options: {
                        cacheName: "musicas-api-cache",
                        expiration: {
                            maxEntries: 50,
                            maxAgeSeconds: 30 * 24 * 60 * 60, // 30 dias
                        },
                    },
                },
            ],
        },
    },
};
