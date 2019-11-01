module.exports = {
    publicPath: "./",
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
};
