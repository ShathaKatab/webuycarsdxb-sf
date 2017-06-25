/**
 * Created by majid on 5/7/17.
 */

angular
    .module('webuycarsApp')
    .component('valuationStepOne', {
        templateUrl: 'templates/valuation/valuationStepOne.html',
        controller:['$document', '$window', 'VehicleModel', function ValuationStepOneController($document, $window, VehicleModel){
            this.vehicleMakeId = null;
            this.vehicleModelId = null;
            this.vehicleYear = null;
            this.vehicleModels = [];
            this.changedVehicleMake = function(){
                var loader = angular.element($document[0].getElementById('loading-container'));
                loader.show();
                this.vehicleModels = VehicleModel.query({vehicleMakeId: this.vehicleMakeId}, function(){
                    loader.hide();
                });
            };

            this.submit = function(theForm, $event){
                theForm.$submitted = true;
                if(theForm.$invalid){
                    $event.preventDefault();
                }
            }
        }],
        controllerAs: 'ctrl',
        bindings: {
            vehicleMakes: '=',
            vehicleYears: '=',
            formButtonText: '@',
            formGroupClass: '@',
            formClass: '@',
            formGroupSubmitClass: '@',
            formActionUrl: '='
        }
    });