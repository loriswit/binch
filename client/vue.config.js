const webpack = require("webpack");

module.exports = {
    productionSourceMap: false,

    pluginOptions: {
        i18n: {
            locale: "en",
            fallbackLocale: "en",
            localeDir: "locales",
            enableInSFC: false
        }
    },

    transpileDependencies: [
        "vuetify"
    ],

    configureWebpack: {
        plugins: [
            new webpack.ContextReplacementPlugin(/moment[/\\]locale$/, /fr/),
        ]
    },

    pwa: {
        name: process.env.VUE_APP_TITLE,
        themeColor: process.env.VUE_APP_THEME,
        msTileColor: process.env.VUE_APP_THEME,
        manifestOptions: {
            start_url: "/",
            background_color: process.env.VUE_APP_THEME,
            icons: [{
                src: "img/icons/icon.svg",
                sizes: "any"
            }, {
                src: "img/icons/icon-512x512.png",
                sizes: "512x512"
            }, {
                src: "img/icons/icon-maskable-512x512.png",
                sizes: "512x512",
                purpose: "maskable"
            }]
        },
        workboxOptions: {
            exclude: [".htaccess"]
        }
    }
};
