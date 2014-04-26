AngularJS Apps
==============

AngularJS is designed for single-page apps.

Here we create one app script per page which defines all the dependencies required on a specific page. The app scripts are named after the page's controller and action.

Landing Page Layout
-------------------

The landing page is meant to be rendered by one single controller in one action only. It doesn't matter on which HTML element the attribute `ng-app` is defined, except that all angular directives are children of this.

Default Page Layout
-------------------

The angular app is defined on the HTML element `.flex-parent-default > .content > .container`. This is the top-level element of each template file `view/<module>/<controller>/<action>.twig`. The menu and the sidebar are currently not used in an angular app. If they need to anytime, the `ng-app` attribute should be defined in the layout template file directly. A generic angular app name should then be used for the 'default page angular module' to which every script residing in this directory should `register` its dependencies.