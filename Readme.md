SpaceAPI Endpoint Hosting
=========================

This repository contains all the files of the website of [http://endpoint.spaceapi.net](http://endpoint.spaceapi.net).

After cloning the repo execute the following commands

```
php composer.phar self-update
php composer.phar install
npm install
bower install
```

Now all the dependencies are installed. In the last stsep you need to compile the sass code.

Run either

```
grunt build
```

for just compiling it or

```
grunt watch
```

to listen for changes during the development. The css file is automatically output to the public folder.