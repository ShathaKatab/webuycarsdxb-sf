/**
 * Created by majid on 5/8/17.
 */

angular
    .module('webuycarsApp')
    .controller('ValuationStepThreeController', ['$scope', '$document', function ($scope, $document) {
        $scope.name = null;
        $scope.mobileNumber = null;

        this.submitForm = function (isValid) {
            if (isValid) {
                valuationStepThreeForm.submit();
            }
        }
    }]);