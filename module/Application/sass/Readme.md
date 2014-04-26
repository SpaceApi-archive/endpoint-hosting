Coding Guide
============

The sass files in this directory are the main files which the css files are generated from. There are two sass file types, page and layout files. Individual pages need individual css rules not used on other pages. This can be the case if pages define a different set of rules for the same view partial such as the json editor. The `layout` type, however, is bound to the layout itself.

Layout partials are put in the `partials/layout` directory while the page partials reside in `partials/pages`.

The main sass files must be added to the sass section in `Gruntfile.js`.

Example
-------

```
sass: {
  dist: {
    files: {
      'public/styles/layout-landing.css' : 'module/Application/sass/_layout-landing.scss',
      'public/styles/layout-default.css' : 'module/Application/sass/_layout-default.scss'
    }
  }
},
```

CSS Howtos & Tutorials
======================

Flexbox
-------

* [Holy Grail Layout](http://philipwalton.github.io/solved-by-flexbox/demos/holy-grail/)
* [Quick hits with the Flexible Box Model](http://www.html5rocks.com/en/tutorials/flexbox/quick/) (slightly out-dated, the specification about the box model has changed)
* [HTML5 Please](http://html5please.com/#flexbox)

Snippets
--------

* [CSS-Tricks](http://css-tricks.com/snippets/)
* [snipplr](http://snipplr.com/popular/language/css)
