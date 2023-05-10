const { defineConfig } = require( '@vue/cli-service' )
module.exports = defineConfig({
  transpileDependencies: true,
  publicPath: process.env.NODE_ENV === 'production'
      ? '/tools/admin/' // production path -  the path we'll deploy to ( `url.ru/tools/admin/` )
      : '/tools/admin/', // local development url localhost:8080/THIS_PATH
  outputDir: 'build',  // path to results of `build` command
})
