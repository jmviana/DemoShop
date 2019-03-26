<?php

require_once "../db.php";
require_once "../HTML/Template/PEAR.php";
require_once "../HTML/Template/IT.php"; ?>
<style>
    <?php include 'register.css'; ?>
</style>

<?php

$db = dbconnect($hostname, $db_name, $db_user, $db_passwd);
if ($db) {

    $template = new HTML_Template_IT('.');

    $template->loadTemplatefile('register.html', true, true);

    if(isset($_GET["emailErr"]) || isset($_GET["pwdErr"]) || isset($_GET["pwdCErr"]) || isset($_GET["sameErr"]) || isset($_GET["nameErr"]) ){
        $errorEmail = isset($_GET['emailErr']) ? $_GET['emailErr'] : "";
        $errorName = isset($_GET['nameErr']) ? $_GET['nameErr'] : "";
        $errorPassword = isset($_GET['passwordErr']) ? $_GET['passwordErr'] : "";
        $errorPasswordConf = isset($_GET['passwordConfErr']) ? $_GET['passwordConfErr'] : "";
        $errorEmailExists = isset($_GET['emailExistsErr']) ? $_GET['emailExistsErr'] : "";

        $errorMessage = "";
        if (!empty($errorEmail)) $errorMessage .= "<span>$errorEmail</span>";
        if (!empty($errorName)) $errorMessage .= "<span>$errorName</span>";
        if (!empty($errorPassword)) $errorMessage .= "<span>$errorPassword</span>";
        if (!empty($errorPasswordConf)) $errorMessage .= "<span>$errorPasswordConf</span>";
        if (!empty($errorEmailExists)) $errorMessage .= "<span>$errorEmailExists</span>";

        $template->setCurrentBlock("ERROR_MESSAGE");
        $template->setVariable('ERROR_MESSAGE', $errorMessage);
        $template->parseCurrentBlock();
    }

    $template->setCurrentBlock("REGISTER");
    if (isset($_GET['nameSuc']))
        $template->setVariable("NAME", $_GET['nameSuc']);
    else
        $template->setVariable("NAME", "");
    if (isset($_GET['emailSuc']))
        $template->setVariable("EMAIL", $_GET['emailSuc']);
    else
        $template->setVariable("EMAIL", "");
    $template->parseCurrentBlock();

    $template->show();

}

?>