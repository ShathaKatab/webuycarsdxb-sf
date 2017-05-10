/**
 * Created by majid on 5/10/17.
 */
angular
    .module('webuycarsApp')
    .factory('BranchTiming', ['$resource',
        function($resource){
            return $resource('branches/:branchSlug/timings/:appointmentDay', {}, {
                query: {
                    method: 'GET',
                    isArray: true
                }
            });
        }]);
