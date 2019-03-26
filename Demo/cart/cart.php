<?php

require_once "../db.php";
require_once "../HTML/Template/PEAR.php";
require_once "../HTML/Template/IT.php";

$db = dbconnect($hostname, $db_name, $db_user, $db_passwd);
if ($db) {
    session_start();

    if (isset($_SESSION['id'])) {
        $product_id = $_POST['product_id'];

        if (!isset($_SESSION['orderId'])) {
            $clientId = $_SESSION['id'];
            $time = date("Y-m-d H:i:s");
            $createOrder_query = "INSERT INTO orders(client_id, created_at, status, total) VALUES('$clientId', '$time', '0', '0');";
            if(!($result = @ mysqli_query($db, $createOrder_query)))
                showerror($db);

            $getOrder_query = "SELECT id FROM orders WHERE status = '0' AND client_id = '$clientId';";
            if(!($result = @ mysqli_query($db, $getOrder_query)))
                showerror($db);
            $tuple = mysqli_fetch_array($result);
            $_SESSION['orderId'] = $tuple['id'];
        }

        $orderId = $_SESSION['orderId'];

        $getPrice_query = "SELECT new_price, price FROM products WHERE id = '$product_id';";
        if(!($result = @ mysqli_query($db, $getPrice_query)))
            showerror($db);
        $tuple = mysqli_fetch_array($result);
        $price = $tuple['new_price'] != null ? $tuple['new_price'] : $tuple['price'];

        $getOrderPrice_query = "SELECT total FROM orders WHERE id = '$orderId';";
        if(!($result = @ mysqli_query($db, $getOrderPrice_query)))
            showerror($db);
        $tuple = mysqli_fetch_array($result);
        $total = $tuple['total'] + $price;

        $insertTotalInOrder_query = "UPDATE orders SET total = '$total' WHERE id = '$orderId';";
        if(!($result = @ mysqli_query($db, $insertTotalInOrder_query)))
            showerror($db);

        $getOrderItem_query = "SELECT * FROM order_items WHERE order_id = '$orderId' AND product_id = $product_id;";
        if(!($result = @ mysqli_query($db, $getOrderItem_query)))
            showerror($db);
        $numrows  = mysqli_num_rows($result);

        if ($numrows == 0) {
            $insertOrderItem_query = "INSERT INTO order_items(order_id, product_id, quantity) VALUES('$orderId', '$product_id', '1');";
            if (!($result = @ mysqli_query($db, $insertOrderItem_query)))
                showerror($db);
        } else {
            $tuple = mysqli_fetch_array($result);
            $quantity = $tuple['quantity'] + 1;

            $updateOrderItem_query = "UPDATE order_items SET quantity = '$quantity' WHERE product_id = '$product_id';";
            if (!($result = @ mysqli_query($db, $updateOrderItem_query)))
                showerror($db);
        }

        header("Location: ../products/products.php?product_id=".$product_id);

    } else {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
        $product_id = $_POST['product_id'];
        array_push($_SESSION['cart'], $product_id);

        header("Location: ../products/products.php?product_id=".$product_id);
    }

    mysqli_close($db);
}

?>