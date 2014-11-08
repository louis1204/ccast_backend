<?php

//load and connect to MySQL database stuff
require("db_config.php");
$login_ok = false;
$first_name = "";
$email = "";
$username = "";
$uid = -1;

if (!empty($_POST)) {
    //gets user's info based off of a email.
    $query = " 
            SELECT 
                uid, 
				username,
				first_name,
				email,
                password
            FROM users 
            WHERE 
                email = :email 
        ";
    
    $query_params = array(
        ':email' => $_POST['email']
    );
    
    try {
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        // For testing, you could use a die and message. 
        //die("Failed to run query: " . $ex->getMessage());
        
        //or just use this use this one to product JSON data:
        $response["success"] = 0;
        $response["message"] = "Database Error1. Please Try Again!";
        die(json_encode($response));
        
    }
    
    //This will be the variable to determine whether or not the user's information is correct.
    //we initialize it as false.
    $validated_info = false;
    
    //fetching all the rows from the query
    $row = $stmt->fetch();
    if ($row) {
        //if we encrypted the password, we would unencrypt it here, but in our case we just
        //compare the two passwords
        if ($_POST['password'] === $row['password']) {
            $login_ok = true;
			$uid = $row['uid'];
			$first_name = $row['first_name'];
			$email = $row['email'];
			$username = $row['username'];
        }
    }
    
    // If the user logged in successfully, then we send them to the private members-only page 
    // Otherwise, we display a login failed message and show the login form again 
    if ($login_ok) {
        $response["success"] = 1;
        $response["message"] = "Login successful!";
		$response["user"]["uid"] = $uid;
		$response["user"]["first_name"] = $first_name;
		$response["user"]["email"] = $email;
		$response["user"]["username"] = $username;
        die(json_encode($response));
    } else {
        $response["success"] = 0;
        $response["message"] = "Invalid Credentials!";
        die(json_encode($response));
    }
} else {
?>
		<h1>Login</h1> 
		<form action="login.php" method="post"> 
		    Email:<br /> 
		    <input type="text" name="email" placeholder="username" /> 
		    <br /><br /> 
		    Password:<br /> 
		    <input type="password" name="password" placeholder="password" value="" /> 
		    <br /><br /> 
		    <input type="submit" value="Login" /> 
		</form> 
		<a href="create_user.php">Register</a>
	<?php
}

?> 
