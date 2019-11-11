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
};
