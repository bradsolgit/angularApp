<?php 
$app->get('/session', function() {
    $db = new DbHandler();
    $session = $db->getSession();
    $response["uid"] = $session['uid'];
    $response["email"] = $session['email'];
    $response["name"] = $session['name'];
    echoResponse(200, $session);
});

$app->post('/login', function() use ($app) {
    require_once 'passwordHash.php';
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('email', 'password'),$r->customer);
    $response = array();
    $db = new DbHandler();
    $password = $r->customer->password;
    $email = $r->customer->email;
    $user = $db->getOneRecord("select uid,name,password,email,created from customers_auth where phone='$email' or email='$email'");
    if ($user != NULL) {
        if(passwordHash::check_password($user['password'],$password)){
        $response['status'] = "success";
        $response['message'] = 'Logged in successfully.';
        $response['name'] = $user['name'];
        $response['uid'] = $user['uid'];
        $response['email'] = $user['email'];
        $response['createdAt'] = $user['created'];
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['uid'] = $user['uid'];
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $user['name'];
        } else {
            $response['status'] = "error";
            $response['message'] = 'Login failed. Incorrect credentials';
        }
    }else {
            $response['status'] = "error";
            $response['message'] = 'No such user is registered';
        }
    echoResponse(200, $response);
});
	
	$app->post('/signUp', function() use ($app) {
		$response = array();
		$r = json_decode($app->request->getBody());
		verifyRequiredParams(array('email', 'name', 'password'),$r->customer);
		require_once 'passwordHash.php';
		$db = new DbHandler();
		$phone = $r->customer->phone;
		$name = $r->customer->name;
		$email = $r->customer->email;
		$address = $r->customer->address;
		$password = $r->customer->password;
		$isUserExists = $db->getOneRecord("select 1 from customers_auth where phone='$phone' or email='$email'");
		if(!$isUserExists){
			$r->customer->password = passwordHash::hash($password);
			$tabble_name = "customers_auth";
			$column_names = array('phone', 'name', 'email', 'password', 'city', 'address');
			$result = $db->insertIntoTable($r->customer, $column_names, $tabble_name);
			if ($result != NULL) {
				$response["status"] = "success";
				$response["message"] = "User account created successfully";
				$response["uid"] = $result;
				if (!isset($_SESSION)) {
					session_start();
				}
				$_SESSION['uid'] = $response["uid"];
				$_SESSION['phone'] = $phone;
				$_SESSION['name'] = $name;
				$_SESSION['email'] = $email;
				echoResponse(200, $response);
			} else {
				$response["status"] = "error";
				$response["message"] = "Failed to create customer. Please try again";
				echoResponse(201, $response);
			}
		}else{
			$response["status"] = "error";
			$response["message"] = "An user with the provided phone or email exists!";
			echoResponse(201, $response);
		}
	});
	
	
$app->get('/customers', function() use ($app) {
    $response = array();
    $customers = array();
    $db = new DbHandler();
    
    if(true){
        $tabble_name = "angularcode_customers";
        $result = $db->getAllRecords("select customerName,email,address,customerNumber from angularcode_customers");
        if ($result != NULL) {
        	$response['status'] = "success";
        	$response['message'] = 'Logged in successfully.';
        	foreach($result as $i=>$var){
        		$customers[$i]['name']= $var[0];
        		$customers[$i]['email']= $var[1];
        		$customers[$i]['address']= $var[2];
        		$customers[$i]['customerNumber']= $var[3];
        	}
        	$response['customers'] = $customers;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create customer. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "An user with the provided phone or email exists!";
        echoResponse(201, $response);
    }
});

	$app->post('/customer', function() use ($app) {
		$r = json_decode($app->request->getBody());
		$response = array();
		$db = new DbHandler();
		$customerID = $r->customerID;
		$user = $db->getOneRecord("select customerName,email,address from angularcode_customers where customerNumber='$customerID'");
		if ($user != NULL) {
			$response['status'] = "success";
			$response['message'] = 'Logged in successfully.';
			$response['name'] = $user['customerName'];
			$response['email'] = $user['email'];
			$response['address'] = $user['address'];
		}else {
			$response['status'] = "error";
			$response['message'] = 'No such user is registered';
		}
		echoResponse(200, $response);
	});
	
	$app->get('/insertCustomer', function() use ($app) {
		$response = array();
		$r = json_decode($app->request->getBody());
		$db = new DbHandler();
		$phone = $r->customer->phone;
		$name = $r->customer->name;
		$email = $r->customer->email;
		$address = $r->customer->address;
		$password = $r->customer->password;
		$isUserExists = $db->getOneRecord("select 1 from customers_auth where phone='$phone' or email='$email'");
		if(!$isUserExists){
			$r->customer->password = passwordHash::hash($password);
			$tabble_name = "customers_auth";
			$column_names = array('phone', 'name', 'email', 'password', 'city', 'address');
			$result = $db->insertIntoTable($r->customer, $column_names, $tabble_name);
			if ($result != NULL) {
				$response["status"] = "success";
				$response["message"] = "User account created successfully";
				$response["uid"] = $result;
				if (!isset($_SESSION)) {
					session_start();
				}
				$_SESSION['uid'] = $response["uid"];
				$_SESSION['phone'] = $phone;
				$_SESSION['name'] = $name;
				$_SESSION['email'] = $email;
				echoResponse(200, $response);
			} else {
				$response["status"] = "error";
				$response["message"] = "Failed to create customer. Please try again";
				echoResponse(201, $response);
			}
		}else{
			$response["status"] = "error";
			$response["message"] = "An user with the provided phone or email exists!";
			echoResponse(201, $response);
		}
	});
	
$app->get('/logout', function() {
    $db = new DbHandler();
    $session = $db->destroySession();
    $response["status"] = "info";
    $response["message"] = "Logged out successfully";
    echoResponse(200, $response);
});
?>