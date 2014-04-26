'use strict';

angular
  .module('EndpointEditApp', ['jsoneditor'])
  .controller('EndpointEditController', function($scope, $element) {

    $scope.aceChanged = function($e) {
      var json = $e[1].session.getValue();
      var textarea = angular.element($element.find('textarea')[0]);
      textarea.text(json);
    };

  });

