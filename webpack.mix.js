const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.options({
    processCssUrls: true // disable file loader for url()
});

mix.setPublicPath('public/assets')
    .setResourceRoot('/assets')
    .js('resources/js/app.js', 'js')
    .js('resources/js/home.js', 'js')
    .js('resources/js/sites.show.js', 'js')
    .js('resources/js/users.show.js', 'js')
    .js('resources/js/reviews.edit.js', 'js')
    .js('resources/js/comments.show.js', 'js')
    .js('resources/js/reviews.show.js', 'js')
    .sass('resources/sass/app.scss', 'css')
    .sass('resources/sass/bootstrap.scss', 'css')
    .version();

if (mix.inProduction()) {

} else {
    mix.sourceMaps();
}

if (mix.inProduction()) {
    mix.options({
        terser: {
            terserOptions: {
                compress: {
                    drop_console: true
                }
            }
        }
    });
}

mix.webpackConfig({
    externals: {
        "jquery": "jQuery"
    },
    resolve: {
        extensions: ['.js', '.json', '.less'],
        modules: [
            path.resolve('./resources/js/components'),
            path.resolve('./node_modules')
        ]
    },
    // включаем поддержку трансформации arrow functions в старый формат для пакетов импортируемых из node_modules
    module: {
        rules: [
            {
                test: /\.jsx?$/,
                exclude: /(bower_components)/,
                use: [
                    {
                        loader: 'babel-loader',
                        options: Config.babel()
                    }
                ]
            }
        ]
    }
    /*
    resolve: {
        modules: [
            'node_modules',
            'webpack2.config.js'
        ]
    }
    */
});
