'use strict';

// # Globbing
// for performance reasons we're only matching one level down:
// 'test/spec/{,*/}*.js'
// use this if you want to recursively match all subfolders:
// 'test/spec/**/*.js'

module.exports = function (grunt) {

  // Time how long tasks take. Can help when optimizing build times
  require('time-grunt')(grunt);

  // Load grunt tasks automatically
  require('load-grunt-tasks')(grunt);

  // Load the grunt tasks manually
  //grunt.loadNpmTasks('grunt-contrib-sass');
  //grunt.loadNpmTasks('grunt-contrib-watch');

  // Define the configuration for all the tasks
  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

    cssmin: {
      minify: {
        expand: true,
        cwd: 'public/styles/',
        src: ['*.css', '!*.min.css'],
        dest: 'public/styles/',
        ext: '.min.css'
      }
    },

    sass: {
      dist: {
        files: {
          'public/styles/layout-landing.css' : 'module/Application/sass/_layout-landing.scss',
          'public/styles/layout-default.css' : 'module/Application/sass/_layout-default.scss',
          'public/styles/page-endpoint-index.css' : 'module/Application/sass/_page-endpoint-index.scss',
          'public/styles/page-endpoint-edit.css' : 'module/Application/sass/_page-endpoint-edit.scss',
          'public/styles/page-endpoint-create.css' : 'module/Application/sass/_page-endpoint-create.scss'
        }
      }
    },

    watch: {
      css: {
        files: 'module/Application/sass/**/*.scss',
        tasks: ['sass', 'cssmin']
      }
    }

  });

  grunt.registerTask('default',['watch']);
  grunt.registerTask('build',['sass', 'cssmin']);
};
