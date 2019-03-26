<?php

require_once '../db.php';
require_once '../HTML/Template/IT.php';

$db = dbconnect($hostname, $db_name, $db_user, $db_passwd);
if ($db) {

    $clientEmail = $_POST['email'];
    $clientPassword = $_POST['password'];
    $password = substr(md5($clientPassword), 0, 32);
    $action = "login.php?";
    $errors = 0;

    //check errors
    $query = "SELECT * FROM clients WHERE email = '$clientEmail';";
    if (!($result = @ mysqli_query($db, $query)))
        showerror($db);

    $numrows = mysqli_num_rows($result);
    for ($i=0; $i<$numrows; $i++) {
        $tuple = mysqli_fetch_array($result);
    }

    $tuple_email = $tuple['email'];
    $tuple_password = $tuple['password_digest'];
    $tuple_id = $tuple['id'];
    $tuple_name = $tuple['name'];

    if (empty($tuple_email)) {
        $action .= "error=0";
        $errors++;
        header("Location:".$action);
    }
    if (strcmp($tuple_password, $password) != 0) {
        $action .= "error=1";
        $errors++;
        header("Location:".$action);
    }

    //if no errors occurred
    if ($errors == 0) {
        session_start();
        $_SESSION['id'] = $tuple_id;
        $_SESSION['name'] = $tuple_name;

        header("Location: ../index.php");
    }

    mysqli_close($db);

}

?>