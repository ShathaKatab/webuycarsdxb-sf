/**
 * Created by majid on 5/11/17.
 */
angular
    .module('webuycarsApp')
    .controller('ValuationStepFiveController', ['$scope', '$filter', '$document', 'NgMap',
        function ($scope, $filter, $document, NgMap) {
            var defaultLat = 25.206497;
            var defaultLng = 55.268743;
            var defaultZoom = 10;

            var vm = this;
            vm.latitude = null;
            vm.longitude = null;
            vm.branchName = null;

            NgMap.getMap().then(function (map) {
                if(vm.latitude && vm.longitude){
                    map.setZoom(defaultZoom);
                    map.setCenter({lat: vm.latitude, lng: vm.longitude});

                    var marker = new google.maps.Marker({
                        position: {lat: vm.latitude, lng: vm.longitude},
                        title: vm.branchName
                    });

                    marker.setMap(map);

                }else{
                    map.setZoom(defaultZoom);
                    map.setCenter({lat: defaultLat, lng: defaultLng});
                }
            });
        }]);