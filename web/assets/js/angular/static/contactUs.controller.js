/**
 * Created by majid on 5/13/17.
 */
angular.module('webuycarsApp')
    .controller('ContactUsController', ['$scope', function($scope){
        var vm = this;

        vm.submitForm = function(isValid){
            if(isValid){
                contactUsForm.submit()
            }
        }
    }]);
