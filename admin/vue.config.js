const { defineConfig } = require( '@vue/cli-service' )

const getPublicPath = () => {
  switch ( process.env.NODE_ENV ) {
    case 'production': return '/tools/admin/' // production path -  the path we'll deploy to ( `url.ru/tools/admin/` )
    case 'development': return '/tools/admin/' // local development url localhost:8080/THIS_PATH
    case 'localbuild': return '/tools/admin/build/' // Apache/etc local build
    default: return '/tools/admin/'
  }
}

module.exports = defineConfig({
  transpileDependencies: true,
  publicPath: getPublicPath(),
  outputDir: 'build',  // path to results of `build` command
})
