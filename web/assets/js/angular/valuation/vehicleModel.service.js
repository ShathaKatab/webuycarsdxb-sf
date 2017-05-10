/**
 * Created by majid on 5/7/17.
 */

angular
    .module('webuycarsApp')
    .factory('VehicleModel', ['$resource',
        function($resource){
            return $resource('vehicles/make/:vehicleMakeId/models', {}, {
                query: {
                    method: 'GET',
                    params: {vehicleMakeId: 'valuation.vehicleMake.id'},
                    isArray: true
                }
            });
        }]);