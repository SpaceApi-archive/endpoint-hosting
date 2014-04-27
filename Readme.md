SpaceAPI Endpoint Hosting
=========================

This repository contains all the files of the website of [http://endpoint.spaceapi.net](http://endpoint.spaceapi.net).

There are two ways how to get the project _alive_. If you have `VirtualBox` and `Vagrant` installed you can do everything automatically, otherwise you have to build the project on your own but you need `php`, `nodejs`, `npm`, `grunt` and `bower` already installed.

After cloning the repo **recursively** and changing to the project root directory execute either

```
vagrant up
```

or the following commands if you prefer to do things yourself.

```
php composer.phar self-update
php composer.phar install
npm install
grunt bower
```

Now all the dependencies are installed. In the last step you need to compile the sass code.

Run either

```
grunt build
```

for just compiling it and

```
grunt watch
```

to listen for changes during the development. The css files are automatically output to `public/styles`.

Running the VM
--------------

<!--
```
git clone https://github.com/SpaceApi/endpoint-hosting.git
cd endpoint-hosting
vagrant up
vagrant ssh -c "cd /vagrant && npm install && bower install && grunt build"
sudo sh -c "echo 127.0.0.1 endpoint.spaceapi.net >> /etc/hosts"
firefox http://endpoint.spaceapi.net:8090
```
-->

```
git clone https://github.com/SpaceApi/endpoint-hosting.git
cd endpoint-hosting
vagrant up
firefox http://localhost:8090
```

The domain `endpoint.spaceapi.net` will be changed to `endpoint.spaceapi.dev` in the near future.