'use strict';

angular
  .module('EndpointIndexApp', ['angular-parallax', 'jsoneditor'])
  .controller('EndpointIndexController', function($scope, $element) {

    $scope.aceChanged = function($e) {
      var json = $e[1].session.getValue();
      var textarea = angular.element($element.find('textarea')[0]);
      textarea.text(json);
    };

  });