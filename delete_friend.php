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
    if (empty($_POST['uid']) || empty($_POST['fid'])) {
        // Create some data that will be the JSON response 
        $response["success"] = 0;
        $response["message"] = "Please Enter uid and fid.";
        
        //die will kill the page and not execute any code below, it will also
        //display the parameter... in this case the JSON data our Android
        //app will parse
        die(json_encode($response));
    }
    
    //if the page hasn't died, we will check with our database to see if there is
    //already a user with the username specificed in the form.  ":user" is just
    //a blank variable that we will change before we execute the query.  We
    //do it this way to increase security, and defend against sql injections
    $query1 = " 
            SELECT 1 FROM friends WHERE uid = :uid AND fid = :fid
        ";
    $query2 = " 
            SELECT 1 FROM friends WHERE uid = :fid AND fid = :uid
        ";
    
    //now lets update what :user should be
    $query_params = array(
        ':uid' => $_POST['uid'],
        ':fid' => $_POST['fid']
    );
    
    
    //Now let's make run the query:
    try {
        // These two statements run the query against your database table. 
        $stmt1   = $db->prepare($query1);
        $result = $stmt1->execute($query_params);

        $stmt2   = $db->prepare($query2);
        $result = $stmt2->execute($query_params);
    }
    catch (PDOException $ex) {
        // For testing, you could use a die and message. 
        die("Failed to run query 1: " . $ex->getMessage());
        
        //or just use this use this one to product JSON data:
        $response["success"] = 0;
        $response["message"] = "Database Error1. Please Try Again!";
        die(json_encode($response));
    }
    
    //fetch is an array of returned data.  If any data is returned,
    //we know that the username is already in use, so we murder our
    //page
    $row1 = $stmt1->fetch();
    $row2 = $stmt2->fetch();
    if ($row1) {
        $query = "DELETE FROM friends WHERE uid = :uid AND fid = :fid ";

        //Now let's make run the query:
        try {
            // These two statements run the query against your database table. 
            $stmt   = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch (PDOException $ex) {
            // For testing, you could use a die and message. 
            die("Failed to run query 2: " . $ex->getMessage());
            
            //or just use this use this one to product JSON data:
            $response["success"] = 0;
            $response["message"] = "Database Error1. Please Try Again!";
            die(json_encode($response));
        }
    }
    else if($row2)
    {
        $query = "DELETE FROM friends WHERE uid = :fid AND fid = :uid ";

        //Now let's make run the query:
        try {
            // These two statements run the query against your database table. 
            $stmt   = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch (PDOException $ex) {
            // For testing, you could use a die and message. 
            die("Failed to run query 3: " . $ex->getMessage());
            
            //or just use this use this one to product JSON data:
            $response["success"] = 0;
            $response["message"] = "Database Error1. Please Try Again!";
            die(json_encode($response));
        }
    }
    //we didn't find the friends so something went wrong
    else
    {
        $response["success"] = 0;
        $response["message"] = "Couldn't find the friends uid: {$_POST['uid']} fid: {$_POST['fid']}";
        die(json_encode($response));
    }
    //-----------------------------------------------------------------------
    //update the notification table for the friend that got deleted
    //
    $query        = " SELECT 1 FROM notifications WHERE uid = :fid";
    //now lets update what :user should be
    $query_params = array(
        ':fid' => $_POST['fid']
    );
        
    //Now let's make run the query:
    try {
        // These two statements run the query against your database table. 
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        // For testing, you could use a die and message. 
        die("Failed to run query 4: " . $ex->getMessage());
        
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
        //we currently don't have a user row for notifications
        $query = " INSERT INTO notifications ( uid, update_list ) VALUES ( :fid, :update_list ) ";
        $query_params = array(':fid' => $_POST['fid'],
                              ':update_list' => 1);
            
        try {
            $stmt   = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch (PDOException $ex) {
            // For testing, you could use a die and message. 
            die("Failed to run query 5: " . $ex->getMessage());
               
            //or just use this use this one:
            $response["success"] = 0;
            $response["message"] = "Database Error2. Please Try Again!";
            die(json_encode($response));
        }
    }
    else {
        //if there is a row, just update it
        $query = " UPDATE `notifications` SET `update_list`= :update_list WHERE `uid` = :fid ";
        $query_params = array(':fid' => $_POST['fid'],
                              ':update_list' => 1);
                                    
        //time to run our query, and create the user
        try {
            $stmt   = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch (PDOException $ex) {
            // For testing, you could use a die and message. 
            die("Failed to run query 6: " . $ex->getMessage());
               
            //or just use this use this one:
            $response["success"] = 0;
            $response["message"] = "Database Error2. Please Try Again!";
            die(json_encode($response));
        }
    }

    $response["success"] = 1;
    $response["message"] = "Success";
    die(json_encode($response));
    
} else {
?>
	<h1>Delete Friend</h1> 
	<form action="delete_friend.php" method="post"> 
		uid:<br /> 
	    <input type="text" name="uid" value="" /> 
	    <br /><br /> 
	    fid:<br /> 
        <input type="text" name="fid" value="" /> 
        <br /><br /> 
        <input type="submit" value="Submit" /> 
	</form>
	<?php
}


?>