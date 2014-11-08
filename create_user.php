<?php
/*
Our "config.inc.php" file connects to database every time we include or require
it within a php script.  Since we want this script to add a new user to our db,
we will be talking with our database, and therefore,
let's require the connection to happen:
*/
require("db_config.php");

//if posted data is not empty
if (!empty($_POST)) {
    //If the username or password is empty when the user submits
    //the form, the page will die.
    //Using die isn't a very good practice, you may want to look into
    //displaying an error message within the form instead.  
    //We could also do front-end form validation from within our Android App,
    //but it is good to have a have the back-end code do a double check.
    if (empty($_POST['email']) || empty($_POST['username']) || empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['password'])) {
        
        
        // Create some data that will be the JSON response 
        $response["success"] = 0;
        $response["message"] = "Please Enter Your Email, Username, First Name, Last Name, and Password.";
        
        //die will kill the page and not execute any code below, it will also
        //display the parameter... in this case the JSON data our Android
        //app will parse
        die(json_encode($response));
    }
    
    //if the page hasn't died, we will check with our database to see if there is
    //already a user with the username specificed in the form.  ":user" is just
    //a blank variable that we will change before we execute the query.  We
    //do it this way to increase security, and defend against sql injections
    $query        = " SELECT 1 FROM users WHERE email = :email";
    //now lets update what :user should be
    $query_params = array(
        ':email' => $_POST['email']
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
    if ($row) {
        // For testing, you could use a die and message. 
        //die("This username is already in use");
        
        //You could comment out the above die and use this one:
        $response["success"] = 0;
        $response["message"] = "I'm sorry, this username is already in use";
        die(json_encode($response));
    }
    
    //If we have made it here without dying, then we are in the clear to 
    //create a new user.  Let's setup our new query to create a user.  
    //Again, to protect against sql injects, user tokens such as :user and :pass
    $query = "INSERT INTO users ( email, username, first_name, last_name, password ) VALUES ( :email, :username, :first_name, :last_name, :password ) ";
    
    //Again, we need to update our tokens with the actual data:
    $query_params = array(
		':email' => $_POST['email'],
		':username' => $_POST['username'],
		':first_name' => $_POST['first_name'],
		':last_name' => $_POST['last_name'],
        ':password' => $_POST['password']
    );
    
    //time to run our query, and create the user
    try {
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        // For testing, you could use a die and message. 
        //die("Failed to run query: " . $ex->getMessage());
        
        //or just use this use this one:
        $response["success"] = 0;
        $response["message"] = "Database Error2. Please Try Again!";
        die(json_encode($response));
    }
	
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
    
    //fetching all the rows from the query
    $row = $stmt->fetch();
    if ($row) {
        //if we encrypted the password, we would unencrypt it here, but in our case we just
        //compare the two passwords
			$uid = $row['uid'];
			$first_name = $row['first_name'];
			$email = $row['email'];
			$username = $row['username'];
    
    }
    
    // If the user logged in successfully, then we send them to the private members-only page 
    // Otherwise, we display a login failed message and show the login form again 
    $response["success"] = 1;
    $response["message"] = "User successfully Added!";
	$response["user"]["uid"] = $uid;
	$response["user"]["first_name"] = $first_name;
	$response["user"]["email"] = $email;
	$response["user"]["username"] = $username;
    die(json_encode($response));
    
    //for a php webservice you could do a simple redirect and die.
    //header("Location: login.php"); 
    //die("Redirecting to login.php");
    
    
} else {
?>
	<h1>Register</h1> 
	<form action="create_user.php" method="post"> 
	Email:<br /> 
	    <input type="text" name="email" value="" /> 
	    <br /><br /> 
		Username:<br /> 
	    <input type="text" name="username" value="" /> 
	    <br /><br /> 
		First Name:<br /> 
	    <input type="text" name="first_name" value="" /> 
	    <br /><br /> 
	    Last Name:<br /> 
	    <input type="text" name="last_name" value="" /> 
	    <br /><br /> 
	    Password:<br /> 
	    <input type="password" name="password" value="" /> 
	    <br /><br /> 
	    <input type="submit" value="Register New User" /> 
	</form>
	<?php
}


?>