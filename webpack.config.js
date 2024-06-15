// webpack.config.js
const Encore = require("@symfony/webpack-encore");

Encore
  // répertoire où seront stockés les fichiers compilés
  .setOutputPath("public/build/")
  // chemin public utilisé par le serveur web pour accéder aux fichiers
  .setPublicPath("/build")
  // point d'entrée de votre application
  .addEntry("app", "./assets/app.js")
  // active la compilation de Sass/SCSS
  .enableSassLoader()
  // nettoie le répertoire de sortie avant chaque build
  .cleanupOutputBeforeBuild()
  // active les sourcemaps en développement
  .enableSourceMaps(!Encore.isProduction())
  // permet la compilation en plusieurs fichiers
  .splitEntryChunks()
  // active la notification de build
  .enableBuildNotifications();

module.exports = Encore.getWebpackConfig();
