angular
    .module('webuycarsAdminApp')
    .component('statItem', {
        templateUrl: 'templates/admin/dashboard/stat-item.html',
        controller: [function ValuationStepOneController() {
            var vm = this;

            vm.chartSeries = ['Total'];
            vm.chartLabels = [];
            vm.chartData = [
                []
            ];

            vm.$onChanges = function (changes) {
                if (changes.theStatItem) {
                    var theStatItem = changes.theStatItem.currentValue;

                    if (angular.isArray(theStatItem.items)) {
                        theStatItem.items.forEach(function (element) {
                            vm.chartLabels.push(element['created_at']);
                            vm.chartData[0].push({t: moment(element['created_at']), y: element['total']});
                        });
                    }
                }
            }
        }],
        controllerAs: 'ctrl',
        bindings: {
            title: '@',
            noDataText: '@',
            theStatItem: '<',
            chartOptions: '='
        }
    });