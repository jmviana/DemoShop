<?php

require_once '../db.php';
require_once  '../HTML/Template/IT.php'; ?>

<style>
    <?php require_once "../index.css"; ?>
</style>

<?php

$db = dbconnect($hostname, $db_name, $db_user, $db_passwd);
if ($db) {

    function isEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    $clientName = $_POST['name'];
    $clientEmail = $_POST['email'];
    $clientPassword = $_POST['password'];
    $clientPasswordConfirmation = $_POST['password_confirmation'];
    $action = "register.php?";
    $errors = 0;

    //CHECK ERRORS
    if (isEmail($clientEmail)) {
        $action .= "emailSuc=$clientEmail&";
    } else {
        $action .= "emailErr=Invalid%20Email&";
        $errors++;
    }
    if (empty($clientName)) {
        $action .= "nameErr=Invalid%20Name&";
        $errors++;
    } else {
        $action .= "nameSuc=$clientName&";
    }
    if (empty($clientPassword)) {
        $action .= "passwordErr=Empty%20Password&";
        $errors++;
    }
    if (strcmp($clientPassword, $clientPasswordConfirmation) != 0) {
        $action .= "passwordConfErr=Passwords%20don't%20match&";
    }

    $query = "SELECT * FROM clients WHERE email='$clientEmail';";
    if(!($result = @ mysqli_query($db, $query)))
        showerror($db);

    $num_rows = mysqli_num_rows($result);
    for ($i=0; $i < $num_rows; $i++) {
        $tuple = mysqli_fetch_array($result);
    }

    if (!empty($tuple['email'])) {
        $action .= "emailExistsErr=Email%20exists%20already&";
        $errors++;
    }

    if ($errors > 0)
        header("Location:" . $action);

    //if no errors
    if ($errors == 0) {
        $password = substr(md5($clientPassword), 0, 32);

        $query = "INSERT INTO clients(name, email, password_digest)
                  VALUES('$clientName', '$clientEmail', '$password');";

        if(!($result = @ mysqli_query($db, $query)))
            showerror($db);

        $template = new HTML_Template_IT('.');
        $template->loadTemplatefile("../message_template.html");

        $template->setCurrentBlock("MESSAGE");
        $template->setVariable("MESSAGE", "<div class=\"message-success\"><h1>Registered successfully!</h1></div>");
        $template->parseCurrentBlock();

        $template->show();
    }

    mysqli_close($db);
}

?>