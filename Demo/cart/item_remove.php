<?php

require_once "../db.php";

$db = dbconnect($hostname, $db_name, $db_user, $db_passwd);
if ($db) {
    session_start();

    if (isset($_SESSION['id'])) {

        if (isset($_SESSION['orderId'])) {
            $orderId = $_SESSION['orderId'];
            $orderItemPrice = $_POST['order_item_price'];
            $orderItemId = $_POST['order_item_id'];

            $removeOrderItem_query = "DELETE FROM order_items WHERE id = '$orderItemId';";
            if (!($result = @ mysqli_query($db, $removeOrderItem_query)))
                showerror($db);

            $updateOrderPrice_query = "UPDATE orders SET total = total - '$orderItemPrice' WHERE id = '$orderId';";
            if (!($result = @ mysqli_query($db, $updateOrderPrice_query)))
                showerror($db);

            $checkOrderItems_query = "SELECT * FROM order_items WHERE order_id = '$orderId';";
            if (!($result = @ mysqli_query($db, $checkOrderItems_query)))
                showerror($db);
            $numrows = mysqli_num_rows($result);
            if ($numrows == 0) {
                unset($_SESSION['cart']);

                $deleteOrder_query = "DELETE FROM orders WHERE id = '$orderId';";
                if (!($result = @ mysqli_query($db, $deleteOrder_query)))
                    showerror($db);

                unset($_SESSION['orderId']);
            }

            header("Location: ../cart/checkout.php");
        }

    } else {

        if (isset($_SESSION['cart'])) {
            $product_id = $_POST['product_id'];
            $cart = $_SESSION['cart'];

            $cart = array_diff($cart, array($product_id));
            $cart = array_values($cart);
            $_SESSION['cart'] = $cart;

            if (sizeof($_SESSION['cart']) == 0) {
                unset($_SESSION['cart']);
            }

            header("Location: ../cart/checkout.php");

        }

    }
    mysqli_close($db);
}

?>