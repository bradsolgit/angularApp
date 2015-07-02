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
        $result = $db->getAllRecords("select customerName,email,address,customerNumber,city,country from angularcode_customers");
        if ($result != NULL) {
        	$response['status'] = "success";
        	$response['message'] = 'Logged in successfully.';
        	foreach($result as $i=>$var){
        		$customers[$i]['name']= $var[0];
        		$customers[$i]['email']= $var[1];
        		$customers[$i]['address']= $var[2];
        		$customers[$i]['customerNumber']= $var[3];
        		$customers[$i]['city']= $var[4];
        		$customers[$i]['country']= $var[5];
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
		$user = $db->getOneRecord("select customerName,email,address,city,country from angularcode_customers where customerNumber='$customerID'");
		if ($user != NULL) {
			$response['status'] = "success";
			$response['message'] = 'Logged in successfully.';
			$response['customerName'] = $user['customerName'];
			$response['email'] = $user['email'];
			$response['address'] = $user['address'];
			$response['city'] = $user['city'];
			$response['country'] = $user['country'];
		}else {
			$response['status'] = "error";
			$response['message'] = 'No such user is registered';
		}
		echoResponse(200, $response);
	});
	
	$app->post('/insertCustomer', function() use ($app) {
		$response = array();
		$r = json_decode($app->request->getBody());
		$db = new DbHandler();
		$name = $r->customer->customerName;
		$email = $r->customer->email;
		$address = $r->customer->address;
		$city = $r->customer->city;
		$country = $r->customer->country;
		$isUserExists = $db->getOneRecord("select 1 from angularcode_customers where customerName='$name' or email='$email'");
		if(!$isUserExists){
			$tabble_name = "angularcode_customers";
			$column_names = array('country', 'customerName', 'email', 'city', 'address');
			$result = $db->insertIntoTable($r->customer, $column_names, $tabble_name);
			if ($result != NULL) {
				$response["status"] = "success";
				$response["message"] = "Customer created successfully";
				echoResponse(200, $response);
			} else {
				$response["status"] = "error";
				$response["message"] = "Failed to create customer. Please try again";
				echoResponse(201, $response);
			}
		}else{
			$response["status"] = "error";
			$response["message"] = "An user with the provided name or email exists!";
			echoResponse(201, $response);
		}
	});
	
	$app->post('/updateCustomer', function() use ($app) {
		$response = array();
		$r = json_decode($app->request->getBody());
		$db = new DbHandler();
		$name = $r->customer->customerName;
		$email = $r->customer->email;
		$address = $r->customer->address;
		$city = $r->customer->city;
		$country = $r->customer->country;
		$id = $r->id;
			$tabble_name = "angularcode_customers";
			$column_names = array('country', 'customerName', 'email', 'city', 'address');
			$result = $db->updateRecordInTable($r->customer, $column_names, $tabble_name,$id);
			if ($result != NULL) {
				$response["status"] = "success";
				$response["message"] = "Customer updated successfully";
				echoResponse(200, $response);
			} else {
				$response["status"] = "error";
				$response["message"] = "Failed to update customer. Please try again";
				echoResponse(201, $response);
			}
		
	});
		
	$app->post('/deleteCustomer', function() use ($app) {
		$response = array();
		$r = json_decode($app->request->getBody());
		$db = new DbHandler();
		$id = $r->id;
		$tabble_name = "angularcode_customers";
		$result = $db->deleteRecord($tabble_name, 'customerNumber', $id);
		if ($result != NULL) {
			$response["status"] = "success";
			$response["message"] = "Customer deleted successfully";
			echoResponse(200, $response);
		} else {
			$response["status"] = "error";
			$response["message"] = "Failed to delete customer. Please try again";
			echoResponse(201, $response);
		}
			
	});
	
	$app->post('/fileUpload1', function() use ($app) {
		if (!isset($_FILES['file'])) {
        echo "No files uploaded!!";
        return;
    }

    $imgs = array();

    $files = $_FILES['file'];
    
    $image = trim($files['name'], " ");
    //$uploadedfile = $_FILES['file']['tmp_name'];
    
    if ($image)
    {
    	$filename = stripslashes($image);
    	//$extension = getExtension($filename);
    	$extension = $files['type'];
    	if (($extension != "image/jpeg") && ($extension != "jpg") && ($extension != "jpeg")
    
    			&& ($extension != "png") && ($extension != "gif"))
    	{
    		echo ' Unknown Image extension ';
    		$errors=1;
    	}
    	else
    	{
    		//$size=filesize($uploadedfile->size);
    
    			
    		if($extension=="jpg" || $extension=="jpeg" || $extension=="image/jpeg" || $extension=="image/jpg")
    		{
    			//$uploadedfile = $_FILES['file']['tmp_name'];
    			$src = imagecreatefromjpeg(trim($files['tempName']," "));
    		}
    		else if($extension=="png" || $extension=="image/png")
    		{
    			//$uploadedfile = $_FILES['file']['tmp_name'];
    			$src = imagecreatefrompng($files['tempName']);
    		}
    		else
    		{
    			$src = imagecreatefromgif($files['tempName']);
    		}
    
    		list($width,$height)=getimagesize($files['tempName']);
    
    		$newwidth=841;
    		$newheight=($height/$width)*$newwidth;
    		$tmp=imagecreatetruecolor($newwidth,$newheight);
    
    		
    		imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,
    
    		$width,$height);
    
    		
    		$filename = UPLOAD_PATH . str_replace(' ', '', $image);
    		
    		imagejpeg($tmp,$filename,100);
    		
    		imagedestroy($src);
    		imagedestroy($tmp);
    		imagedestroy($tmp1);
    	}
    }
    


    if ($result != NULL) {
			$response["status"] = "success";
			$response["message"] = "Customer deleted successfully";
			echoResponse(200, $response);
		} else {
			$response["status"] = "error";
			$response["message"] = "Failed to delete customer. Please try again";
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