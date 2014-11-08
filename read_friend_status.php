<?php

//load and connect to MySQL database stuff
require("db_config.php");

if (!empty($_POST['uid']) && !empty($_POST['fid'])) {
    //gets user's info based off of a email.
    $query1 = " 
				SELECT friend_state
				FROM friends
				WHERE friends.uid = :uid AND friends.fid = :fid
        ";
    
    $query2 = " 
                SELECT friend_state
                FROM friends
                WHERE friends.uid = :fid AND friends.fid = :uid
        ";

    $query_params = array(
        ':uid' => (int)$_POST['uid'],
        ':fid' => (int)$_POST['fid']
    );
    
    try {
        $stmt1   = $db->prepare($query1);
        $result = $stmt1->execute($query_params);
        $stmt2   = $db->prepare($query2);
        $result = $stmt2->execute($query_params);

    }
    catch (PDOException $ex) {
        // For testing, you could use a die and message. 
        die("Failed to run query: " . $ex->getMessage());
        
        //or just use this use this one to product JSON data:
        $response["success"] = 0;
        $response["message"] = "Database Error1. Please Try Again!";
        die(json_encode($response));
        
    }
    
    $row1 = $stmt1 -> fetch();
    $row2 = $stmt2 -> fetch();
    
    if($row1)
    {
        $response["success"] = 1;
        $response["friend_state"] = $row1["friend_state"];
        die(json_encode($response));
    }
    else if($row2)
    {
        $response["success"] = 1;
        $response["friend_state"] = $row1["friend_state"];
        die(json_encode($response));
    }
    
    $response["success"] = 0;
    $response["message"] = "Unable to find friend";
    die(json_encode($response));

} else {
?>
		<h1>Read Friend Status</h1> 
		<form action="read_friend_status.php" method="post"> 
		    User ID:<br /> 
		    <input type="text" name="uid" placeholder="uid" /> 
		    <br />
            Friend ID:<br /> 
            <input type="text" name="fid" placeholder="fid" /> 
            <br /><br /> 
		    <input type="submit" value="Get!" /> 
		</form> 
	<?php
}

?> 
