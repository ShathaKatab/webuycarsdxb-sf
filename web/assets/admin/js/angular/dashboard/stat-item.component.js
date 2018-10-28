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
                            var xAxis = null;

                            switch (vm.chartUnit.id){
                                case 'year':
                                    xAxis = moment(element['year'], 'YYYY').endOf('year');
                                    break;
                                case 'month':
                                    xAxis = moment(element['year'] + '-' + element['month'], 'YYYY-M').endOf('month');
                                    break;
                                case 'quarter':
                                    xAxis = moment(element['year'] + '-' + element['quarter'], 'YYYY-Q').endOf('quarter');
                                    break;
                                case 'week':
                                    xAxis = moment(element['year'] + '-' + element['week'], 'YYYY-w').endOf('week');
                                    break;
                                default:
                                    xAxis = moment(element['created_at'], 'YYYY-M-D');
                            }

                            vm.chartLabels.push(xAxis);
                            vm.chartData[0].push({x: xAxis, y: element['total']});
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
            chartOptions: '=',
            chartUnit: '='
        }
    });