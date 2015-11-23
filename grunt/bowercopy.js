module.exports = {
    options: {
        srcPrefix: 'bower_components'
    },
    web: {
        options: {
            destPrefix: 'web/assets'
        },
        files: {
            // jquery
            'js/jquery.js'                      : 'jquery/dist/jquery.min.js',

            // bootstrap
            'css/bootstrap.css'                 : 'bootstrap/dist/css/bootstrap.min.css',
            'js/bootstrap.js'                   : 'bootstrap/dist/js/bootstrap.min.js',

            // checkbox
            'css/bootstrap-checkbox.css'        : 'awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css',

            // selectize
            'js/selectize.js'                   : 'selectize/dist/js/selectize.min.js',
            'css/selectize.css'                 : 'selectize/dist/css/selectize.bootstrap3.css',

            // tablesaw
            'js/tablesaw.js'                    : 'tablesaw/dist/tablesaw.js',
            'css/tablesaw.css'                  : 'tablesaw/dist/tablesaw.css',

            // font-awesome
            'css/font-awesome.css'              : 'fontawesome/css/font-awesome.min.css',
            'fonts/FontAwesome.otf'             : 'fontawesome/fonts/FontAwesome.otf',
            'fonts/fontawesome-webfont.eot'     : 'fontawesome/fonts/fontawesome-webfont.eot',
            'fonts/fontawesome-webfont.svg'     : 'fontawesome/fonts/fontawesome-webfont.svg',
            'fonts/fontawesome-webfont.ttf'     : 'fontawesome/fonts/fontawesome-webfont.ttf',
            'fonts/fontawesome-webfont.woff'    : 'fontawesome/fonts/fontawesome-webfont.woff',
            'fonts/fontawesome-webfont.woff2'   : 'fontawesome/fonts/fontawesome-webfont.woff2'
        }
    }
}