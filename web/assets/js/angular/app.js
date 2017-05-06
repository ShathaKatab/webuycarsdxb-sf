/**
 * Created by majid on 4/30/17.
 */

var webuycarsApp = angular.module(['webuycarsApp'], []);

webuycarsApp.controller('ValuationController', function ValuationController($scope){
    $scope.phones = [
        {
            name: 'Nexus S',
            snippet: 'Fast just got faster with Nexus S.'
        }, {
            name: 'Motorola XOOM™ with Wi-Fi',
            snippet: 'The Next, Next Generation tablet.'
        }, {
            name: 'MOTOROLA XOOM™',
            snippet: 'The Next, Next Generation tablet.'
        }
    ];
});