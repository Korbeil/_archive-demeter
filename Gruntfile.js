/**
 * Created by baptisteleduc on 05/05/15.
 */

module.exports = function(grunt) {

    // measures the time each task takes
    require('time-grunt')(grunt);

    // load grunt config
    require('load-grunt-config')(grunt);

    // help task
    grunt.registerTask('help', 'Display some help', function(args) {
        grunt.log.subhead('~demeter project');
        grunt.log.writeln('------');
        grunt.log.writeln('');
        grunt.log.writeln('Available commands:');

        grunt.log.ok('help      - show this help');
        grunt.log.ok('bowercopy - will move assets to their ~standard path');
    });
}