<?php

require_once 'db.php';
require_once "HTML/Template/PEAR.php";
require_once "HTML/Template/IT.php";
session_start(); ?>
<style>
<?php include 'index.css'; ?>
</style>

<?php
//include 'inc/db.inc';

$db = dbconnect($hostname, $db_name, $db_user, $db_passwd);
if ($db) {

    $template = new HTML_Template_IT('.');

    $template->loadTemplatefile('index.html', true, true);

    //gets the unfinished order e the cart items
    if (isset($_SESSION['id'])) {
        $clientId = $_SESSION['id'];
        if (!isset($_SESSION['orderId'])) {
            $getOrder_query = "SELECT id FROM orders WHERE status = '0' AND client_id = '$clientId';";
            if (!($result = @ mysqli_query($db, $getOrder_query)))
                showerror($db);
            $numrows = mysqli_num_rows($result);
            if ($numrows > 0) {
                $tuple = mysqli_fetch_array($result);
                $_SESSION['orderId'] = $tuple['id'];
            }
        }
        if (isset($_SESSION['cart'])) {

            $cart = $_SESSION['cart'];
            $items_list = array();
            $quantities_list = array();
            foreach ($cart as $item_id) {
                if (!in_array($item_id, $items_list)) {
                    array_push($items_list, $item_id);
                    array_push($quantities_list, 1);
                } else {
                    $item_index = array_search($item_id, $items_list);
                    $quantities_list[$item_index]++;
                }
            }

            if (isset($_SESSION['orderId'])) {
                $orderId = $_SESSION['orderId'];

                //get current total price of the order
                $getOrderPrice_query = "SELECT total FROM orders WHERE id = '$orderId';";
                if(!($result = @ mysqli_query($db, $getOrderPrice_query)))
                    showerror($db);
                $tuple = mysqli_fetch_array($result);
                $total_price = $tuple['total'];

                //insert items
                for ($i=0; $i<sizeof($items_list); $i++) {
                    //insert item
                    $insertCartItems_query = "INSERT INTO order_items(order_id, product_id, quantity) VALUES('$orderId', '$items_list[$i]', '$quantities_list[$i]');";
                    if (!($result = mysqli_query($db, $insertCartItems_query)))
                        showerror($db);

                    //get price of product
                    $getPrice_query = "SELECT new_price, price FROM products WHERE id = '$items_list[$i]';";
                    if(!($result = @ mysqli_query($db, $getPrice_query)))
                        showerror($db);
                    $tuple = mysqli_fetch_array($result);
                    $price = $tuple['new_price'] != null ? $tuple['new_price'] : $tuple['price'];
                    $total_price += $price * $quantities_list[$i];
                }

                //insert the final total price of the order
                $insertTotalInOrder_query = "UPDATE orders SET total = '$total_price' WHERE id = '$orderId';";
                if(!($result = @ mysqli_query($db, $insertTotalInOrder_query)))
                    showerror($db);

            } else {
                //create order
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

                //current total price of the order
                $total_price = 0;
                //insert items
                $orderId = $_SESSION['orderId'];
                for ($i=0; $i<sizeof($items_list); $i++) {
                    $insertCartItems_query = "INSERT INTO order_items(order_id, product_id, quantity) VALUES('$orderId', '$items_list[$i]', '$quantities_list[$i]');";
                    if (!($result = mysqli_query($db, $insertCartItems_query)))
                        showerror($db);

                    //get price of product
                    $getPrice_query = "SELECT new_price, price FROM products WHERE id = '$items_list[$i]';";
                    if(!($result = @ mysqli_query($db, $getPrice_query)))
                        showerror($db);
                    $tuple = mysqli_fetch_array($result);
                    $price = $tuple['new_price'] != null ? $tuple['new_price'] : $tuple['price'];
                    $total_price += $price * $quantities_list[$i];
                }

                //insert the final total price of the order
                $insertTotalInOrder_query = "UPDATE orders SET total = '$total_price' WHERE id = '$orderId';";
                if(!($result = @ mysqli_query($db, $insertTotalInOrder_query)))
                    showerror($db);

            }



        }
    }

    $getCategories_query = "SELECT category FROM products;";
    if (!($result = mysqli_query($db, $getCategories_query)))
        showerror($db);

    $categories = array();
    $nrows = mysqli_num_rows($result);
    for ($i=0; $i<$nrows; $i++) {
        $tuple = mysqli_fetch_array($result);
        if ($tuple['category'] != null)
            if (!in_array($tuple['category'], $categories))
                array_push($categories, $tuple['category']);
    }

    $cat_string = "";
    foreach ($categories as $cat) {
        $cat_string .= "<a class=\"cat-link\" href=\"index.php?cat=".$cat."\">".$cat."</a>";
    }

    $template->setCurrentBlock("MENU");
    if (isset($_GET['discount'])) {
        $query = "SELECT * FROM products WHERE new_price IS NOT NULL;";
        $template->setVariable("TABS", "
                <li>
                    <a id=\"home-link\" href=\"index.php\">Home</a>
                </li>
                <li>
                    <div class=\"menu-dropdown\">
                        <a class=\"menu-dropbtn\">Categories
                        </a>
                        <div class=\"menu-dropdown-content\">
                          ".$cat_string."
                        </div>
                    </div>
                </li>
                <li>
                    <a id=\"discounts-link\" href=\"index.php?discount=1\" class=\"selected\">Discounts</a>
                </li>");
    }elseif (isset($_GET['cat'])) {
        $category = $_GET['cat'];
        $query = "SELECT * FROM products WHERE category = '$category';";
        $template->setVariable("TABS", "
                <li>
                    <a id=\"home-link\" href=\"index.php\">Home</a>
                </li>
                <li>
                    <div class=\"menu-dropdown\">
                        <a class=\"menu-dropbtn selected\">Categories
                        </a>
                        <div class=\"menu-dropdown-content\">
                          ".$cat_string."
                        </div>
                    </div>
                </li>
                <li>
                    <a id=\"discounts-link\" href=\"index.php?discount=1\">Discounts</a>
                </li>");
    } else {
        $query = "SELECT * FROM products";
        $template->setVariable("TABS", "
                <li>
                    <a id=\"home-link\" href=\"index.php\" class=\"selected\">Home</a>
                </li>
                <li>
                    <div class=\"menu-dropdown\">
                        <a class=\"menu-dropbtn\">Categories
                        </a>
                        <div class=\"menu-dropdown-content\">
                          ".$cat_string."
                        </div>
                    </div>
                </li>
                <li>
                    <a id=\"discounts-link\" href=\"index.php?discount=1\">Discounts</a>
                </li>");
    }

    // executes the query
    if (!($result = @ mysqli_query($db, $query)))
        showerror($db);

    if (isset($_SESSION['id'])) {
        $total_quantity = 0;
        if (isset($_SESSION['orderId'])) {
            $orderId = $_SESSION['orderId'];
            $getOrderItems_query = "SELECT * FROM order_items WHERE order_id = '$orderId';";
            if (!($res = @ mysqli_query($db, $getOrderItems_query)))
                showerror($db);
            $numrows = mysqli_num_rows($res);
            for ($i=0; $i<$numrows; $i++) {
                $res_tuple = mysqli_fetch_array($res);
                $total_quantity += $res_tuple['quantity'];
            }
        }

        $template->setVariable("CART", $total_quantity);
        $template->setVariable("MENU1", "<a href=\"cart/view_orders.php\">View Orders</a>");
        $template->setVariable("MENU2", "<a href=\"login/logout_action.php\">Logout</a>");
        $template->setVariable("MENU3", "<div class='welcome'>".$_SESSION['name']."</div>");
    } else {
        $total_quantity = 0;

        if (isset($_SESSION['cart'])) {
            $cart = $_SESSION['cart'];
            $total_quantity = sizeof($cart);
        }

        $template->setVariable("CART", $total_quantity);
        $template->setVariable("MENU1", "<a href=\"login/login.php\">Login</a>");
        $template->setVariable("MENU2", "<a href=\"register/register.php\" class=\"button highlight\">Register</a>");
    }
    $template->parseCurrentBlock();

    $nrows  = mysqli_num_rows($result);
    for($i=0; $i<$nrows; $i++) {
        $tuple = mysqli_fetch_array($result);

        $old_price = $tuple['price'];
        $new_price = $tuple['new_price'];

        $image = $tuple['image'];
        $name = $tuple['name'];
        $product_id = $tuple['id'];
        if ($new_price == null) { $new_price = $old_price . "€"; $old_price = ""; }
        else { $new_price.="€"; $old_price.="€"; }


        $template->setCurrentBlock("PRODUCTS");
        $template->setVariable("PRODUCT_ID", $product_id);
        $template->setVariable('IMAGE', $image);
        $template->setVariable('PRODUCT_NAME', $name);
        $template->setVariable('OLD_PRICE', $old_price);
        $template->setVariable('NEW_PRICE', $new_price);
        $template->parseCurrentBlock();
    }

    $template->show();
}

?>