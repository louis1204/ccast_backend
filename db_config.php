<?php
 
/*
 * All database connection variables
 */
 /*
define('DB_USER', "895865_geck1"); // db user
define('DB_PASSWORD', ""); // db password (mention your db password here)
define('DB_DATABASE', "geck1_zxq_ccast"); // database name
define('DB_SERVER', "geck1.zxq.net"); // db server
*/
$host = "localhost";
$dbname = "ccast";
$username = "root";
$password = "";

$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

try
{
	$db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options);
}
catch(PDOException $ex)
{
	die("Failed to connect to the database: " . $ex->getMessage());
	echo("Dead");
}

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
{
        function undo_magic_quotes_gpc(&$array)
        {
            foreach($array as &$value)
            {
                if(is_array($value))
                {
                    undo_magic_quotes_gpc($value);
                }
                else
                {
                    $value = stripslashes($value);
                }
            }
        }

		undo_magic_quotes_gpc($_POST);

        undo_magic_quotes_gpc($_GET);

        undo_magic_quotes_gpc($_COOKIE);
}	

header('Content-Type: text/html; charset=utf-8');

session_start();

?>