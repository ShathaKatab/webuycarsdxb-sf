/**
 * Created by majid on 5/8/17.
 */

angular
    .module('webuycarsApp')
    .controller('ValuationStepThreeController', ['$scope', '$document', function ($scope, $document) {
        $scope.name = null;
        $scope.mobileNumber = null;

        this.submitForm = function (isValid) {
            if (isValid && gRecaptchaSubmitted) {
                valuationStepThreeForm.submit();
            }

            if (!gRecaptchaSubmitted) {
                angular.element($document[0]).find('.g-recaptcha').addClass('form-control-invalid');
            }
        }
    }]);

var gRecaptchaSubmitted = false;

function gRecaptchaCallback() {
    gRecaptchaSubmitted = true;
    $('.g-recaptcha').removeClass('form-control-invalid');
}