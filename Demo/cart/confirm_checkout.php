<?php

require_once "../db.php";
require_once "../HTML/Template/PEAR.php";
require_once "../HTML/Template/IT.php"; ?>

<style>
    <?php require_once "../index.css"; ?>
</style>

<?php

$db = dbconnect($hostname, $db_name, $db_user, $db_passwd);
if ($db) {

    $template = new HTML_Template_IT('.');

    $template->loadTemplatefile('../message_template.html', true, true);
    session_start();

    if (isset($_SESSION['orderId'])) {

        $orderId = $_SESSION['orderId'];
        $updateOrderStatus_query = "UPDATE orders SET status = 1 WHERE id = '$orderId';";
        if (!($result = @ mysqli_query($db, $updateOrderStatus_query)))
            show_error($db);

        unset($_SESSION['orderId']);

    }

    $template->setCurrentBlock("MESSAGE");
    $template->setVariable("MESSAGE", "<div class=\"message-success\"><h1>Order nº".$orderId." placed successfully!</h1></div>");
    $template->parseCurrentBlock();

    $template->show();

    mysqli_close($db);
}

?>