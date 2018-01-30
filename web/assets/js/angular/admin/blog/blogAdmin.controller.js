/**
 * Created by majid on 29/01/2018.
 */

angular
    .module('webuycarsAdminApp')
    .controller('BlogAdminController', ['$scope', 'Slug', function ($scope, Slug) {
        var vm = this;
        vm.name = null;
        vm.title = null;
        vm.slug = null;

        vm.titleChanged = function(){
            vm.slug = Slug.slugify(vm.title);
        }
    }]);