/**
 * Created by majid on 5/8/17.
 */

angular
    .module('webuycarsApp')
    .controller('ValuationStepTwoController', ['$scope', function ($scope) {
        $scope.vehicleModelType = null;
        $scope.vehicleMileage = null;
        $scope.vehicleOption = null;
        $scope.vehicleBodyCondition = null;

        this.submitForm = function (isValid) {
            if (isValid) {
                valuationStepTwoForm.submit();
            }
        }
    }]);