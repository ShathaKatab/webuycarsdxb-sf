/**
 * Created by majid on 5/7/17.
 */

angular
    .module('webuycarsApp')
    .component('valuationStepOne', {
        templateUrl: 'templates/valuation/valuationStepOne.html',
        controller:['$document', '$window', 'VehicleModel', function ValuationStepOneController($document, $window, VehicleModel){
            this.vehicleModels = [];
            this.changedVehicleMake = function(){
                var loader = angular.element($document[0].getElementById('loading-container'));
                loader.show();
                this.vehicleModels = VehicleModel.query({vehicleMakeId: this.vehicleMakeId}, function(){
                    loader.hide();
                });
            };

            this.submit = function(isValid){
                if(isValid){
                    $window.location.href = '/car-valuation/model/'+this.vehicleModelId+'/'+this.vehicleYear;
                }
            };
        }],
        controllerAs: 'ctrl',
        bindings: {
            vehicleMakes: '=',
            vehicleYears: '=',
            formButtonText: '@',
            formGroupClass: '@',
            formClass: '@',
            formGroupSubmitClass: '@'
        }
    });