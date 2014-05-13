'use strict';

angular.extend( angular, {
  toParam: toParam
});

/**
 * Object -> String
 * Similar to [url]http://api.jquery.com/jQuery.param/[/url]
 * Source from [url]https://github.com/angular/angular.js/issues/983#issuecomment-35504841[/url]
 *
 * @param object
 * @param [prefix]
 * @returns {string}
 */
function toParam( object, prefix ) {
  var stack = [];
  var value;
  var key;

  for( key in object ) {
    value = object[ key ];
    key = prefix ? prefix + '[' + key + ']' : key;

    if ( value === null ) {
      value = encodeURIComponent( key ) + '=';
    } else if ( typeof( value ) !== 'object' ) {
      value = encodeURIComponent( key ) + '=' + encodeURIComponent( value );
    } else {
      value = toParam( value, key );
    }

    stack.push( value );
  }

  return stack.join( '&' );
}

angular
  .module('EndpointIndexApp', ['angular-parallax', 'jsoneditor'])
  .controller('EndpointIndexController', function($scope, $element, $http) {

    $scope.json = angular.element($element.find('textarea')[0]).text();
    $scope.jsonValid = true;

    $scope.aceChanged = function($e) {
      $scope.json = $e[1].session.getValue();

      try {
        JSON.parse($scope.json);
        $scope.jsonValid = true;
      } catch (e) {
        $scope.jsonValid = false;
      }

      var textarea = angular.element($element.find('textarea')[0]);
      textarea.text($scope.json);
    };

    $scope.results = {
      class: 'ok',
      message: 'Your JSON is compliant to the specs.',
      errors: []
    };

    $scope.validate = function() {
      if ($scope.jsonValid) {
        $http({
          url: '/endpoint/validate-ajax',
          method: "POST",
          data: angular.toParam({json: $scope.json}),
          headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function (validation, status, headers, config) {

          if (validation.valid.indexOf('0.13') >= 0) {
            $scope.results.message = 'Your JSON is compliant to the specs 0.13';
            $scope.results.class = 'ok';
          } else {
            for (var version in validation.errors) {
              if (version == '0.13') {
                $scope.results.errors = validation.errors[version];
              }
            }

            $scope.results.message = 'Your JSON is not compliant to the specs 0.13';
            $scope.results.class = 'error';
          }
        }).error(function (data, status, headers, config) {
          // @todo do something
        });
      } else {
        $scope.results.message = 'Your JSON is invalid!';
        $scope.results.class = 'error';
      }

      return false;
    }
  });
