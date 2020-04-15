module.exports = {
  devServer: {
    public: process.env.BASE_URL,
    disableHostCheck: true,
    watchOptions: {
      poll: 1000 // Check for changes every second
    },
  },
  filenameHashing: false,
}