app.controller('customerCtrl', function ($scope, $rootScope, $routeParams, $location, $http, fileUpload, Data) {
    //initially set those objects to null to avoid undefined error
    $scope.login = {};
    $scope.signup = {};
    $scope.customer = {};
    var customerID = $routeParams.customerID;
    $rootScope.title = (customerID > 0) ? 'Edit Customer' : 'Add Customer';
    $scope.buttonText = (customerID > 0) ? 'Update Customer' : 'Add New Customer';
    $scope.customerID = customerID;
    //$scope.customer = [{name:'Rohit',email:'Rohit',address:'59-8-1/1',city:'Vijayawada',country:'India'}];
   // $scope.customer.name = 'Rohit';
    //$scope.customer.email = 'rohit.bhattad@oracle.com';
      
      if(customerID != 0){
       Data.post('customer', {
       	customerID: customerID
       	}).then(function (results) {
       		$scope.customer.customerName = results.customerName;
       		$scope.customer.email = results.email;
       		$scope.customer.address = results.address;
       		$scope.customer.city = results.city;
       		$scope.customer.country = results.country;
           });
      }
      
      $scope.uploadFile = function(files) {
    	    var fd = new FormData();
    	    //Take the first selected file
    	    fd.append("file", files[0]);

    	    $http.post(uploadUrl, fd, {
    	        withCredentials: true,
    	        headers: {'Content-Type': undefined },
    	        transformRequest: angular.identity
    	    }).success( "").error("");

    	};
    	
	 $scope.uploadFileDir = function(){
	        var file = $scope.myFile;
	        console.log('file is ' + JSON.stringify(file));
	        var uploadUrl = "api/v1/fileUpload1";
	        fileUpload.uploadFileToUrl(file, uploadUrl);
	    };
	
      $scope.saveCustomer = function(customer) {
          if (customerID <= 0) {
        	  Data.post('insertCustomer', {
                  customer: customer,
                  
              }).then(function (results) {
                  Data.toast(results);
                  if (results.status == "success") {
                	  var file = $scope.myFile;
          	        console.log('file is ' + JSON.stringify(file));
          	        var uploadUrl = "api/v1/fileUpload1";
          	        fileUpload.uploadFileToUrl(file, uploadUrl);
                      $location.path('dashboard');
                  }
              });          
        } else {
        	Data.post('updateCustomer', {
                customer: customer,
                id : customerID
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                	var file = $scope.myFile;
        	        console.log('file is ' + JSON.stringify(file));
        	        var uploadUrl = "api/v1/fileUpload1";
        	        fileUpload.uploadFileToUrl(file, uploadUrl);
                    $location.path('dashboard');
                }
            });
          }
      };
    
});