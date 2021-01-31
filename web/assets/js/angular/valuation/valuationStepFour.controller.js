/**
 * Created by majid on 5/9/17.
 */

angular
    .module('webuycarsApp')
    .controller('ValuationStepFourController', [
        '$scope',
        '$filter',
        '$document',
        '$window',
        '$location',
        '$anchorScroll',
        'NgMap',
        'BranchTiming',
        'Appointment',
        function (
            $scope,
            $filter,
            $document,
            $window,
            $location,
            $anchorScroll,
            NgMap,
            BranchTiming,
            Appointment
        ) {
            var vm = this;
            var defaultLat = 25.206497;
            var defaultLng = 55.268743;
            var defaultZoom = 10;
            var selectedBranchZoom = 18;
            var maxDate = new Date();
            maxDate.setDate(maxDate.getDate() + 30);

            vm.mapHeight = '400px';

            vm.branches = [];
            vm.selectedBranch = null;
            vm.appointmentDate = null;
            vm.branchTimings = [];
            vm.selectedBranchTiming = null;
            vm.emailAddress = null;
            vm.markers = [];
            vm.selectedBranchObject = {};
            vm.selectedPosition = {};
            vm.isTimingSlotsAvailable = true;

            vm.valuationId = null;

            //Datepicker options
            vm.options = {
                minDate: new Date(),
                maxDate: maxDate,
                showWeeks: false,
                startingDay: 7,
                maxMode: 'day'
            };

            $scope.$watch('ctrl.branches', function(){
                vm.branchesChanged();
            });

            $scope.$watch('ctrl.selectedBranch', function(){
                vm.isTimingSlotsAvailable = true;
                vm.selectedBranchChanged();
                vm.fetchBranchTimings();
            });

            $scope.$watch('ctrl.appointmentDate', function(){
                vm.isTimingSlotsAvailable = true;
                vm.fetchBranchTimings();
            });

            //Google Maps center changed event listener
            NgMap.getMap().then(function (map) {
                var bookingAppointFormPadding = 94;
                vm.mapHeight = angular.element($document[0].getElementById('book-appointment-form')).height() + bookingAppointFormPadding * 2 + 'px';

                map.addListener('center_changed', function () {
                    // 3 seconds after the center of the map has changed, pan back to the
                    // marker.
                    window.setTimeout(function () {
                        map.panTo(map.getCenter());
                    }, 3000);
                });

                window.setTimeout(function () {
                    var center = map.getCenter();
                    google.maps.event.trigger(map, "resize");
                    map.setCenter(center);
                }, 500);
            });

            vm.addMarker = function (branch) {
                NgMap.getMap().then(function (map) {
                    var latLng = new google.maps.LatLng(branch['latitude'], branch['longitude']);
                    var marker = new google.maps.Marker({
                        position: latLng,
                        title: branch['title']
                    });
                    marker.setMap(map);

                    branch.marker = marker;
                    vm.markers.push(marker);
                });

            };

            vm.fetchBranchTimings = function(){
                if(vm.selectedBranch && vm.appointmentDate instanceof Date){
                    var dateBooked = (new Date(vm.appointmentDate - vm.appointmentDate.getTimezoneOffset() * 60000))
                        .toISOString().split('T')[0];

                    var loader = angular.element($document[0].getElementById('loading-container'));
                    loader.show();

                    vm.branchTimings = BranchTiming.query({branchSlug: vm.selectedBranch, appointmentDay: dateBooked}, function(response){
                        loader.hide();
                        vm.isTimingSlotsAvailable = Boolean(response.length);
                    });
                }
            };

            vm.branchesChanged = function(){
                for (var i in vm.branches) {
                    var branch = vm.branches[i];

                    if (branch.hasOwnProperty('latitude') && branch['latitude'] && branch.hasOwnProperty('longitude') && branch['longitude']) {
                        vm.addMarker(branch);
                    }
                }

                NgMap.getMap().then(function (map) {
                    var bounds = new google.maps.LatLngBounds();
                    for (var i = 0; i < vm.markers.length; i++) {
                        bounds.extend(vm.markers[i].getPosition());
                    }

                    map.fitBounds(bounds);
                });
            };

            vm.selectedBranchChanged = function(){
                vm.selectedBranchObject = $filter('filter')(vm.branches, {slug: vm.selectedBranch})[0];

                NgMap.getMap().then(function (map) {
                    if (vm.selectedBranchObject && vm.selectedBranchObject.marker) {
                        map.setZoom(selectedBranchZoom);
                        map.setCenter(vm.selectedBranchObject.marker.getPosition());
                        vm.selectedPosition = vm.selectedBranchObject.marker.getPosition();
                    } else {
                        vm.selectedPosition = {lat: defaultLat, lng: defaultLng};
                        map.setZoom(defaultZoom);
                        map.setCenter(vm.selectedPosition);
                    }
                });
            };

            vm.triggerFormSubmit = function(){
                $scope.valuationStepFourForm.$setSubmitted();

                if($scope.valuationStepFourForm.$valid && Object.prototype.toString.call(vm.appointmentDate) === '[object Date]'){
                    var timing = vm.selectedBranchTiming.split(':');
                    var appointmentObject = new Appointment;
                    var month =  vm.appointmentDate.getMonth() + 1;
                    appointmentObject.bookedAt = vm.appointmentDate.getFullYear()
                        + "-" + month
                        + "-" + vm.appointmentDate.getDate()
                        + " " + timing[0]
                        + ":" + timing[1]
                        + ":00";
                    appointmentObject.branch = vm.selectedBranch;

                    appointmentObject.emailAddress = vm.emailAddress;

                    var loader = angular.element($document[0].getElementById('loading-container'));
                    loader.show();

                    appointmentObject.$save(null, function(resource, headers){
                        loader.hide();

                        window.setTimeout(function () {
                            $window.location.href = headers('Location');
                        }, 100);
                    }, function(error){
                        loader.hide();
console.log(error);
                        alert('Error: Unable to create an Appointment');
                    });
                }else{
                    vm.scrollTo('pick-a-branch');
                }
            };

            vm.scrollTo = function(hash){
                $location.hash(hash);
                $anchorScroll();
            };
        }]);
