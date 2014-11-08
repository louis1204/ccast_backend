<?php

//load and connect to MySQL database stuff
require("db_config.php");

if (!empty($_POST['username'])) {
    //gets user's info based off of a email.
    $query = " 
            SELECT uid, first_name, last_name, username, mood, about_me, picture_url
			FROM users
			WHERE username LIKE :username
			ORDER BY length(username)
        ";
    
    $query_params = array(
        ':username' => "%{$_POST['username']}%"
    );
    
    try {
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        // For testing, you could use a die and message. 
        die("Failed to run query: " . $ex->getMessage());
        
        //or just use this use this one to product JSON data:
        $response["success"] = 0;
        $response["message"] = "Database Error1. Please Try Again!";
        die(json_encode($response));
        
    }
    
    //fetching all the rows from the query
    $friends = $stmt->fetchAll();
    
    // If the user logged in successfully, then we send them to the private members-only page 
    // Otherwise, we display a login failed message and show the login form again 
	$response = array("usernames" => $friends);
    die(json_encode($response));

} else {
?>
		<h1>Get Friends</h1> 
		<form action="find_usernames.php" method="post"> 
		    Username:<br /> 
		    <input type="text" name="username" placeholder="username" /> 
		    <br /><br /> 
		    <input type="submit" value="Find!" /> 
		</form> 
	<?php
}

?> 