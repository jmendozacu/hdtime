"use strict" #jshint
module.exports = (grunt) ->
  grunt.initConfig
    jshint:
      options:
        jshintrc: ".jshintrc" #jshint config file

      all: ["Gruntfile.js", 
            "assets/js/*.js", 
            "!assets/js/scripts.min.js"]

    less:
      all:
        files:
          "assets/css/style.css": "assets/less/style.less"

    concat:
          all:
              files:
                  "assets/js/scripts.min.js": [
                      "assets/js/plugins/bootstrap/transition.js",
                      "assets/js/plugins/bootstrap/alert.js",
                      "assets/js/plugins/bootstrap/button.js",
                      "assets/js/plugins/bootstrap/carousel.js",
                      "assets/js/plugins/bootstrap/collapse.js",
                      "assets/js/plugins/bootstrap/dropdown.js",
                      "assets/js/plugins/bootstrap/modal.js",
                      "assets/js/plugins/bootstrap/tooltip.js",
                      "assets/js/plugins/bootstrap/popover.js",
                      "assets/js/plugins/bootstrap/scrollspy.js",
                      "assets/js/plugins/bootstrap/tab.js",
                      "assets/js/plugins/bootstrap/typehead.js",
                      "assets/js/plugins/bootstrap/affix.js",
                      "assets/js/plugins/jcarousel/dist/jquery.jcarousel.js",
                      "assets/js/plugins/jcarousel/examples/responsive/jcarousel.responsive.js",
                      "assets/js/plugins/jcarousel/examples/responsive/jcarousel-sales.responsive.js",
                      "assets/js/plugins/jcarousel/examples/connected-carousels/jcarousel.connected-carousels.js",
                      "assets/js/plugins/smartmenu/jquery.smartmenus.js",
                      "assets/js/plugins/smartmenu/jquery.smartmenus.bootstrap.js",
                      "assets/js/plugins/jquery.ui/custom.js",
                      "assets/js/plugins/*.js",
                      "!assets/js/scripts.min.js",
                      "assets/js/*.js"]

    uglify:
      all:
        files:
            "assets/js/scripts.min.js": [
                "assets/js/plugins/bootstrap/transition.js",
                "assets/js/plugins/bootstrap/alert.js",
                "assets/js/plugins/bootstrap/button.js",
                "assets/js/plugins/bootstrap/carousel.js",
                "assets/js/plugins/bootstrap/collapse.js",
                "assets/js/plugins/bootstrap/dropdown.js",
                "assets/js/plugins/bootstrap/modal.js",
                "assets/js/plugins/bootstrap/tooltip.js",
                "assets/js/plugins/bootstrap/popover.js",
                "assets/js/plugins/bootstrap/scrollspy.js",
                "assets/js/plugins/bootstrap/tab.js",
                "assets/js/plugins/bootstrap/typehead.js",
                "assets/js/plugins/bootstrap/affix.js",
                "assets/js/plugins/jcarousel/dist/jquery.jcarousel.js",
                "assets/js/plugins/jcarousel/examples/responsive/jcarousel.responsive.js",
                "assets/js/plugins/jcarousel/examples/responsive/jcarousel-sales.responsive.js",
                "assets/js/plugins/jcarousel/examples/connected-carousels/jcarousel.connected-carousels.js",
                "assets/js/plugins/smartmenu/jquery.smartmenus.js",
                "assets/js/plugins/smartmenu/jquery.smartmenus.bootstrap.js",
                "assets/js/plugins/jquery.ui/custom.js",
                "assets/js/plugins/*.js",
                "!assets/js/scripts.min.js",
                "assets/js/*.js"]
    cssmin:{
      compress: {
        files: {
          "assets/css/style.min.css": "assets/css/style.css"
        }
      }
    }


    watch:
      less:
        files: ["assets/less/*.less", 
                "assets/less/bootstrap/*.less",
                "assets/less/style/*.less",
                "assets/less/style/core/*.less",
                "assets/less/style/libs/*.less",
                "assets/less/style/modules/*.less",
                "assets/less/style/regions/*.less",
                ]
        tasks: ["less", "cssmin"]

      js:
        files: ["<%= jshint.all %>"]
        tasks: ["jshint", "uglify"]

    clean:
      dist: ["assets/css/style.css",
             "assets/js/scripts.min.js"]


  grunt.loadNpmTasks "grunt-contrib-cssmin";
  grunt.loadNpmTasks "grunt-contrib-clean"
  grunt.loadNpmTasks "grunt-contrib-jshint"
  grunt.loadNpmTasks "grunt-contrib-concat"
  grunt.loadNpmTasks "grunt-contrib-uglify"
  grunt.loadNpmTasks "grunt-contrib-watch"
  grunt.loadNpmTasks "grunt-contrib-less"
  grunt.registerTask "default", ["clean", "less", "concat"]
  grunt.registerTask "build", ["clean", "less", "uglify"]
  grunt.registerTask "dev", ["watch"]
