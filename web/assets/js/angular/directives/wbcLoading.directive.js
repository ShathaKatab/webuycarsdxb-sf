/**
 * Created by majid on 5/9/17.
 */
angular
    .module('webuycarsApp')
    .directive('wbcLoading', ['$http', function ($http) {
        return {
            restrict: 'E',
            transclude: true,
            scope: {
                'visibility': '@'
            },
            templateUrl: 'templates/partials/directives/loading.html',
            link: function (scope, elem) {
                if (scope.visibility == 'hidden') {
                    elem.find('#loading-container').hide();
                } else if (scope.visibility == 'visible') {
                    elem.find('#loading-container').show();
                }
            }
        }
    }]);