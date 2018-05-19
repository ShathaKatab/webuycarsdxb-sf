angular.module('webuycarsApp')
    .controller('DetailsController', ['$scope', function ($scope) {
        var vm = this;

        vm.submitForm = function (isValid) {
            if (isValid) {
                candidateForm.submit()
            }
        }
    }]);