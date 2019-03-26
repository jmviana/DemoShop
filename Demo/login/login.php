<?php

require_once "../db.php";
require_once "../HTML/Template/PEAR.php";
require_once "../HTML/Template/IT.php"; ?>
<style>
    <?php include 'login.css'; ?>
</style>

<?php

$db = dbconnect($hostname, $db_name, $db_user, $db_passwd);
if ($db) {

    $template = new HTML_Template_IT('.');

    $template->loadTemplatefile('login.html', true, true);

    $template->setCurrentBlock("FORM_TITLE");
    $template->setVariable("LOGIN", "Login");
    $template->parseCurrentBlock();

    if (isset($_GET['error'])) {
        $error = $_GET['error'];
        if($error == 0) {
            $message = "Invalid Email!";
        }
        if($error ==1) {
            $message = "Invalid Password!";
        }

        $template->setCurrentBlock("ERROR_MESSAGE");
        $template->setVariable("ERROR_MESSAGE", $message);
        $template->parseCurrentBlock();

    }

    $template->show();

}

?>