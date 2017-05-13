/**
 * Created by majid on 5/12/17.
 */
angular
    .module('webuycarsApp')
    .factory('Appointment', ['$resource',
        function($resource){
            return $resource('car-valuation/:valuationId/appointment');
        }]);