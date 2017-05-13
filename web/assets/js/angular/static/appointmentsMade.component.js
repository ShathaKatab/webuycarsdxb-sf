/**
 * Created by majid on 5/13/17.
 */

angular
    .module('webuycarsApp')
    .component('appointmentsMade', {
        templateUrl: 'templates/static/appointmentsMade.html',
        controller: ['$scope',
            function AppointmentsMadeController($scope) {
                var vm = this;
                vm.appointmentsIndexUrl = '';
                vm.totalAppointments = 0;
                vm.initialAppointmentsMade = 0;
                vm.appointmentsMadeSoFar = 0;

                $scope.$watch('ctrl.initialAppointmentsMade', function(){
                    vm.appointmentsMadeSoFar = vm.initialAppointmentsMade + vm.totalAppointments;
                });

                $scope.$watch('ctrl.totalAppointments', function () {
                    vm.appointmentsMadeSoFar = vm.initialAppointmentsMade + vm.totalAppointments;
                });
            }],
        controllerAs: 'ctrl',
        bindings: {
            appointmentsIndexUrl: '=',
            totalAppointments: '=',
            initialAppointmentsMade: '='
        }
    });