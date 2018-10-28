angular
    .module('webuycarsAdminApp')
    .controller('StatsController', [
        '$scope',
        '$document',
        '$window',
        '$filter',
        '$timeout',
        '$cookies',
        '$http',
        function ($scope, $document, $window, $filter, $timeout, $cookies, $http) {
            var vm = this;
            vm.showCharts = false;
            vm.valuations = [];
            vm.valuationsWithoutPrice = [];
            vm.appointments = [];
            vm.appointmentsNoShow = [];
            vm.inspections = [];
            vm.deals = [];

            vm.datePicker = {};
            vm.datePicker.date = {startDate: moment().startOf('month'), endDate: moment()};

            var startDate = $cookies.get('dateRangeStart');
            var endDate = $cookies.get('dateRangeEnd');

            if (startDate && endDate) {
                vm.datePicker.date = {startDate: moment(startDate), endDate: moment(endDate)};
            }

            vm.datePicker.label = vm.datePicker.date.startDate.format('MMMM Do YYYY') + ' - ' + vm.datePicker.date.endDate.format('MMMM Do YYYY');

            vm.chartClick = function (points, evt) {};

            vm.chartUnits = [
                {
                    id: 'day',
                    name: 'Day'
                },
                {
                    id: 'week',
                    name: 'Week'
                }              ,
                {
                    id: 'month',
                    name: 'Month'
                }               ,
                {
                    id: 'quarter',
                    name: 'Quarter'
                },
                {
                    id: 'year',
                    name: 'Year'
                }
            ];

            vm.chartUnitSelected = $cookies.get('chartUnit') ? JSON.parse($cookies.get('chartUnit')) : vm.chartUnits[0];

            vm.chartOptions = {
                elements: {line: {tension: 0}},
                animation: false,
                ///Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines: true,
                //Boolean - Whether to show vertical lines (except Y axis)
                scaleShowVerticalLines: true,
                showTooltips: false,
                showLines: true,
                responsive: true,
                scales: {
                    xAxes: [{
                        type: 'time',
                        distribution: 'linear',
                        time: {
                            unit: vm.chartUnitSelected ? vm.chartUnitSelected.id : 'day',
                            min: moment(vm.datePicker.date.startDate),
                            max: moment(vm.datePicker.date.endDate)
                        }
                    }]
                }
            };

            (vm.fetchStats = function ()  {
                if (vm.datePicker.date.startDate && vm.datePicker.date.endDate) {
                    var loader = angular.element($document[0].getElementById('loading-container'));
                    loader.show();

                    $http({
                        method: 'GET',
                        url: '/admin/dashboard/stats/' + vm.datePicker.date.startDate.format('YYYY-MM-DD') + '/' + vm.datePicker.date.endDate.format('YYYY-MM-DD') + '/' + vm.chartUnitSelected.id
                    }).then(function successCallback(response) {
                        vm.valuations = JSON.parse(response.data.valuations);
                        vm.valuationsWithoutPrice = JSON.parse(response.data.valuationsWithoutPrice);
                        vm.appointments = JSON.parse(response.data.appointments);
                        vm.appointmentsNoShow = JSON.parse(response.data.appointmentsNoShow);
                        vm.inspections = JSON.parse(response.data.inspections);
                        vm.deals = JSON.parse(response.data.deals);

                        loader.hide();
                        vm.showCharts = true;
                    }, function errorCallback(response) {
                        console.log(response);
                        loader.hide();
                        alert('An error occurred!');
                        vm.showCharts = true;
                    });
                }
            })();

            vm.datePickerOptions = {
                locale: {
                    applyLabel: 'Done',
                    fromLabel: 'From',
                    toLabel: 'To',
                    format: 'YYYY-MM-DD',
                    customRangeLabel: 'Custom...'
                },
                opens: 'left',
                showDropdowns: true,
                maxDate: moment(),
                ranges: {
                    'Today': [moment(), moment()],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'Last 90 Days': [moment().subtract(89, 'days'), moment()],
                    'Last 12 Months': [moment().subtract(12, 'months'), moment()],
                    'This Year': [moment().month(0).date(1), moment()],
                    'Last Year': [moment().subtract('1', 'years').month(0).date(1), moment().subtract('1', 'years').month(11).date(31)]
                },
                eventHandlers: {
                    'apply.daterangepicker': function (ev, picker) {
                        vm.datePicker.label = vm.datePicker.date.startDate.format('MMMM Do YYYY') + ' - ' + vm.datePicker.date.endDate.format('MMMM Do YYYY');
                        $cookies.put('dateRangeStart', vm.datePicker.date.startDate.format('YYYY-MM-DD'));
                        $cookies.put('dateRangeEnd', vm.datePicker.date.endDate.format('YYYY-MM-DD'));
                        vm.fetchStats();
                    }
                }
            };

            vm.chartUnitChanged = function () {
                if (vm.chartUnitSelected){
                    $cookies.put('chartUnit', JSON.stringify(vm.chartUnitSelected));
                    $window.location.reload();
                }
            };

            angular.element('.select2-chosen').text(vm.chartUnitSelected ? vm.chartUnitSelected.name : '');
        }]);
