<?php

//load and connect to MySQL database stuff
require("db_config.php");

if (!empty($_POST)) {
    //gets user's info based off of a email.
    $query = " 
            SELECT uid, first_name, last_name, username, mood, about_me, picture_url
			FROM users
			WHERE uid IN
			(
				SELECT uid
				FROM friends
				WHERE friends.fid = :uid AND friends.friend_state = 1
				UNION 
				SELECT fid
				FROM friends
				WHERE friends.uid = :uid AND friends.friend_state = 1
			)
			ORDER BY first_name
        ";
    
    $query_params = array(
        ':uid' => (int)$_POST['uid']
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
	$response = array("friends" => $friends);
    die(json_encode($response));

} else {
?>
		<h1>Get Friends</h1> 
		<form action="get_friends.php" method="post"> 
		    User ID:<br /> 
		    <input type="text" name="uid" placeholder="uid" /> 
		    <br /><br /> 
		    <input type="submit" value="Get!" /> 
		</form> 
	<?php
}

?> 
