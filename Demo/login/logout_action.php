<?php

require_once "../HTML/Template/PEAR.php";
require_once "../HTML/Template/IT.php";
session_start(); ?>

<style>
    <?php require_once "../index.css" ?>
</style>

<?php

$template = new HTML_Template_IT('.');

$template->loadTemplatefile("../message_template.html", true, true);

$template->setCurrentBlock("MESSAGE");
$template->setVariable("MESSAGE", "<div class=\"message-success\"><h1>Logged out successfully!</h1></div>");
$template->parseCurrentBlock();

$template->show();
session_destroy();

?>