/**
 * Created by majid on 5/11/17.
 */
angular
    .module('webuycarsApp')
    .controller('ValuationStepFourController', ['$scope', '$filter', '$document', 'NgMap',
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

            this.printAppointment = function(){
                //    var content = document.getElementById().innerHTML;
                //    var popupWinindow = window.open('', '_blank', 'width=600,height=700,scrollbars=no,menubar=no,toolbar=no,location=no,status=no,titlebar=no');
                //    popupWinindow.document.open();
                //    popupWinindow.document.write('<html><head><link rel="stylesheet" type="text/css" href="style.css" /></head><body onload="window.print()">' + innerContents + '</html>');
                //    popupWinindow.document.close();
                //
                //
                //$.fn.print = function () {
                //    var content = $(this).html();
                //    var w = window.open('about:blank', '', 'width=800,height=600,top=100,left=100');
                //    w.document.write(content);
                //    w.print();
                //    w.close();
                //};
                //
                //$.fn.print2 = function () {
                //    $('*').not(this).addClass('hidden-for-print').hide();
                //    $(this).children().removeClass('hidden-for-print').show();
                //    $(this).parents().removeClass('hidden-for-print').show();
                //    window.print();
                //    $('.hidden-for-print').show();
                //};
            }
        }]);