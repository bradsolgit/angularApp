app.controller('customerCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    //initially set those objects to null to avoid undefined error
    $scope.login = {};
    $scope.signup = {};
    
   $scope.customers= [{customerName:'Rohit',password:'Rohit',name:'Rohit',phone:'9966866886',address:''}];
    
   Data.get('customers', {
       
   }).then(function (results) {
       Data.toast(results);
        $scope.customers = results.customers;
   });

   customer: function(Data, $scope,$route){
       var customerID = $route.current.params.customerID;
       return Data.post('customer', {
       	customerID: customerID
       	}).then(function (results) {
       		$scope.customer = results;
           });
     }
   
    $scope.doLogin = function (customer) {
        Data.post('login', {
            customer: customer
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('dashboard');
            }
        });
    };
    $scope.signup = {email:'',password:'',name:'',phone:'',address:''};
    $scope.signUp = function (customer) {
        Data.post('signUp', {
            customer: customer
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('dashboard');
            }
        });
    };
    $scope.logout = function () {
        Data.get('logout').then(function (results) {
            Data.toast(results);
            $location.path('login');
        });
    }
});