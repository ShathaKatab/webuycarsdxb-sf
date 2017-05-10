/**
 * Created by majid on 5/8/17.
 */

angular
    .module('webuycarsApp')
    .controller('ValuationStepTwoController', ['$scope', '$document', function ($scope, $document) {
        $scope.vehicleModelType = null;
        $scope.vehicleMileage = null;
        $scope.vehicleColor = null;
        $scope.vehicleBodyCondition = null;
        $scope.name = null;
        $scope.emailAddress = null;
        $scope.mobileNumber = null;

        this.submitForm = function (isValid) {
            if (isValid) {
                valuationDetailsForm.submit();
            }
        }
    }]);