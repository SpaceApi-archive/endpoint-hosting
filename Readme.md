SpaceAPI Endpoint Hosting
=========================

This repository contains all the files of the website of [http://endpoint.spaceapi.net](http://endpoint.spaceapi.net).

After cloning the repo **recursively** execute the following commands

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

for just compiling it or

```
grunt watch
```

to listen for changes during the development. The css file is automatically output to the public folder.

Running the VM
--------------

```
git clone https://github.com/SpaceApi/endpoint-hosting.git
cd endpoint-hosting
vagrant up
sudo echo "127.0.0.1 endpoint.spaceapi.net" >> /etc/hosts
firefox http://endpoint.spaceapi.net:8090
```

The domain `endpoint.spaceapi.net` will be changed to `endpoint.spaceapi.dev` in the near future.