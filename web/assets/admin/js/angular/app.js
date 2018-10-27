var webuycarsAdminApp = angular.module(['webuycarsAdminApp'], [
    'angular-toArrayFilter',
    'ngResource',
    'chart.js',
    'ngCookies',
    'daterangepicker'
])
    .config(['ChartJsProvider', function (ChartJsProvider) {
        // Configure all charts
        ChartJsProvider.setOptions({
            chartColors: ['#803690', '#00ADF9', '#DCDCDC', '#46BFBD', '#FDB45C', '#949FB1', '#4D5360'],
            responsive: false
        });
        // Configure all line charts
        ChartJsProvider.setOptions('line', {
            showLines: false
        });
    }]);