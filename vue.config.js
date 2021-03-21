module.exports = {
  devServer: {
    // public: process.env.BASE_URL,
    port: process.env.NODE_PORT,
    disableHostCheck: true,
    watchOptions: {
      poll: 1000 // Check for changes every second
    },
  },
  filenameHashing: false,
  outputDir: 'plugin/app',

}