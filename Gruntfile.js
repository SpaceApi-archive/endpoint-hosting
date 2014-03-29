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

    sass: {
      dist: {
        files: {
          'public/styles/main.css' : 'module/Application/sass/main.scss'
        }
      }
    },

    watch: {
      css: {
        files: 'module/Application/sass/*.scss',
        tasks: ['sass']
      }
    }

  });

  grunt.registerTask('default',['watch']);
};
