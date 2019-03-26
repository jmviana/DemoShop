<?php
// shows the error message from mysql
function showerror($connection)
{
    die("Error " . mysqli_errno($connection) . " : " . mysqli_error($connection));
}
$hostname = "localhost";
$db_name = "demoshop";
$db_user = "root";
$db_passwd = "";
// connects to the database
function dbconnect($hostname, $db_name,$db_user,$db_passwd)
{
    $db = @ mysqli_connect($hostname, $db_user,$db_passwd, $db_name);
    if(!$db) {
        die("Can't connect to the database.");
    }
    if(!(@ mysqli_select_db($db,$db_name))){
        showerror($db);
    }
    return $db;
}

?>