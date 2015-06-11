app.controller('authCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    //initially set those objects to null to avoid undefined error
    $scope.login = {};
    $scope.signup = {};
    
    
   Data.get('customers', {
       
   }).then(function (results) {
       Data.toast(results);
        $scope.customers = results.customers;
   });

   
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
    
    $scope.deleteCustomer = function (id) {
    	if (confirm("sure to delete?")) {
            // todo code for deletion
        
        Data.post('deleteCustomer', {
            id:   id
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
            	Data.get('customers', {
            	       
            	   }).then(function (results) {
            	        $scope.customers = results.customers;
            	   });

            }
        });
    	}
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