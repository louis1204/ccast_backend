<?php
/*
Our "config.inc.php" file connects to database every time we include or require
it within a php script.  Since we want this script to add a new user to our db,
we will be talking with our database, and therefore,
let's require the connection to happen:
*/
require("db_config.php");
$uid;
$update_list;
//if posted data is not empty
if (!empty($_POST)) {
    //If the username or password is empty when the user submits
    //the form, the page will die.
    //Using die isn't a very good practice, you may want to look into
    //displaying an error message within the form instead.  
    //We could also do front-end form validation from within our Android App,
    //but it is good to have a have the back-end code do a double check.
    if (empty($_POST['uid'])) {
        
        
        // Create some data that will be the JSON response 
        $response["success"] = 0;
        $response["message"] = "Please Enter UID.";
        
        //die will kill the page and not execute any code below, it will also
        //display the parameter... in this case the JSON data our Android
        //app will parse
        die(json_encode($response));
    }
    
    //if the page hasn't died, we will check with our database to see if there is
    //already a user with the username specificed in the form.  ":user" is just
    //a blank variable that we will change before we execute the query.  We
    //do it this way to increase security, and defend against sql injections
    $query = " 
            SELECT 
                uid, 
				update_list
            FROM notifications
            WHERE 
                uid = :uid 
        ";
    //now lets update what :user should be
    $query_params = array(
        ':uid' => $_POST['uid']
    );
    
    //Now let's make run the query:
    try {
        // These two statements run the query against your database table. 
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
    
    //fetch is an array of returned data.  If any data is returned,
    //we know that the username is already in use, so we murder our
    //page
    $row = $stmt->fetch();
    if (!$row) {
		$response["success"] = 0;
		$response["message"] = "nothing to read";
		die(json_encode($response));
    }
    
    // If the user logged in successfully, then we send them to the private members-only page 
    // Otherwise, we display a login failed message and show the login form again 
    $uid = $row['uid'];
	$update_list = $row['update_list'];
	$response["success"] = 1;
	$response["uid"] = $uid;
	$response["update_list"] = $update_list;
	die(json_encode($response));
	
} else {
?>
	<h1>Read Update Friendslist</h1> 
	<form action="read_update_friendslist.php" method="post"> 
		uid:<br /> 
	    <input type="text" name="uid" value="" /> 
	    <br /><br /> 
	    <input type="submit" value="Submit" /> 
	</form>
	<?php
}


?>