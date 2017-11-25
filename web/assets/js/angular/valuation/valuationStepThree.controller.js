/**
 * Created by majid on 5/8/17.
 */

angular
    .module('webuycarsApp')
    .controller('ValuationStepThreeController', ['$scope', function ($scope) {
        $scope.name = null;
        $scope.mobileNumber = null;

        this.submitForm = function (isValid) {
            if (isValid) {
                valuationStepThreeForm.submit();
            }
        }
    }]);