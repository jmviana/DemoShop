<?php

require_once "../db.php";
require_once "../HTML/Template/PEAR.php";
require_once "../HTML/Template/IT.php";
session_start(); ?>
    <style>
        <?php require_once "cart.css"; ?>
    </style>
<?php

$db = dbconnect($hostname, $db_name, $db_user, $db_passwd);
if ($db) {

    $template = new HTML_Template_IT('.');

    $template->loadTemplatefile('checkout.html', true, true);

    //fill the categories
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

    foreach ($categories as $cat) {

        $cat_string = "<a class=\"cat-link\" href=\"../index.php?cat=".$cat."\">".$cat."</a>";
        $template->setCurrentBlock("CATEGORIES");
        $template->setVariable("CATEGORIES", $cat_string);
        $template->parseCurrentBlock();
    }

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

        $template->setCurrentBlock("MENU");
        $template->setVariable("CART", $total_quantity);
        $template->setVariable("MENU1", "<a href=\"view_orders.php\">View Orders</a>");
        $template->setVariable("MENU2", "<a href=\"../login/logout_action.php\">Logout</a>");
        $template->setVariable("MENU3", "<div>".$_SESSION['name']."</div>");
        $template->parseCurrentBlock();

        if (isset($_SESSION['orderId'])) {
            $orderId = $_SESSION['orderId'];

            //gets the products info from the order
            $query = "SELECT *, oi.id AS oi_id FROM products AS p, order_items AS oi, orders AS o
              WHERE oi.product_id = p.id AND oi.order_id = '$orderId' AND o.id = '$orderId';";

            if (!($result = @ mysqli_query($db, $query)))
                showerror($db);

            $numrows = mysqli_num_rows($result);

            if ($numrows > 0) {
                for ($i = 0; $i < $numrows; $i++) {
                    $tuple = mysqli_fetch_array($result);

                    $item_price = $tuple['new_price'] != null ? $tuple['new_price'] : $tuple['price'];
                    $template->setCurrentBlock("CHECKOUT");
                    $template->setVariable("ORDER_ITEM_PRICE", $item_price * $tuple['quantity']);
                    $template->setVariable("ITEM_ID", $tuple['oi_id']);
                    $template->setVariable("IMAGE", $tuple['image']);
                    $template->setVariable("NAME", $tuple['name']);
                    $template->setVariable("PRICE", $item_price);
                    $template->setVariable("QUANTITY", $tuple['quantity']);
                    $template->parseCurrentBlock();

                }

                $template->setCurrentBlock("TABLE_HEADER");
                $template->setVariable("HEADER", "
            <tr>
                <th><span>Product Image</span></th>
                <th><span>Product Name</span></th>
                <th><span>Product Price</span></th>
                <th><span>Quantity</span></th>
                <th></th>
            </tr>");
                $template->parseCurrentBlock();

                $template->setCurrentBlock("ORDER_INFO");
                $template->setVariable("ORDER_TITLE", "ORDER CHECKOUT");
                $template->setVariable("NUMBER_ITEMS", $total_quantity);
                $template->setVariable("ORDER_PRICE", $tuple['total']);
                $template->parseCurrentBlock();

                $template->setCurrentBlock("SUBMIT");
                $template->setVariable("SUBMIT_BUTTON", "<a class='confirm_checkout' href=\"confirm_checkout.php\">CONFIRM CHECKOUT</a>");
                $template->parseCurrentBlock();
            } else {
                $template->setCurrentBlock("NO_ORDER");
                $template->setVariable("NO_ORDER", "<h2 style='text-align: center; margin: auto; padding: 1vw;'>There are no items in the cart!</h2>");
                $template->parseCurrentBlock();
            }
        } else {

            $template->setCurrentBlock("NO_ORDER");
            $template->setVariable("NO_ORDER", "<h2 style='text-align: center; margin: auto; padding: 1vw;'>There are no items in the cart!</h2>");
            $template->parseCurrentBlock();

        }

    } else {
        $total_quantity = 0;
        $total_price = 0;

        if (isset($_SESSION['cart'])) {
            $cart = $_SESSION['cart'];
            $total_quantity = sizeof($cart);
        }

        $template->setCurrentBlock("MENU");
        $template->setVariable("CART", $total_quantity);
        $template->setVariable("MENU1", "<a href=\"../login/login.php\">Login</a>");
        $template->setVariable("MENU2", "<a href=\"../register/register.php\" class=\"button highlight\">Register</a>");
        $template->parseCurrentBlock();

        if (isset($_SESSION['cart'])) {
            $total_price = 0;
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
            $total_quantity = 0;
            $total_price = 0;
            for ($i = 0; $i < sizeof($items_list); $i++) {
                $query = "SELECT * FROM products WHERE id = '$items_list[$i]';";
                if (!($result = @ mysqli_query($db, $query)))
                    showerror($db);

                $tuple = mysqli_fetch_array($result);

                $total_quantity += $quantities_list[$i];
                $price = $tuple['new_price'] != null ? $tuple['new_price'] : $tuple['price'];
                $total_price += $price * $quantities_list[$i];

                $template->setCurrentBlock("CHECKOUT");
                $template->setVariable("PRODUCT_ID", $items_list[$i]);
                $template->setVariable("IMAGE", $tuple['image']);
                $template->setVariable("NAME", $tuple['name']);
                $template->setVariable("PRICE", $price);
                $template->setVariable("QUANTITY", $quantities_list[$i]);
                $template->parseCurrentBlock();
            }

            $template->setCurrentBlock("ORDER_INFO");
            $template->setVariable("ORDER_TITLE", "ORDER CHECKOUT");
            $template->setVariable("NUMBER_ITEMS", $total_quantity);
            $template->setVariable("ORDER_PRICE", $total_price);
            $template->parseCurrentBlock();

            $template->setCurrentBlock("TABLE_HEADER");
            $template->setVariable("HEADER", "
            <tr>
                <th><span>Product Image</span></th>
                <th><span>Product Name</span></th>
                <th><span>Product Price</span></th>
                <th><span>Quantity</span></th>
                <th></th>
            </tr>");
            $template->parseCurrentBlock();

            $template->setCurrentBlock("SUBMIT");
            $template->setVariable("SUBMIT_BUTTON", "<a class='confirm_checkout' onmouseover=\"document.getElementsByClassName('tooltiptext')[0].style.opacity='1'\" onmouseout=\"document.getElementsByClassName('tooltiptext')[0].style.opacity='0'\">CONFIRM CHECKOUT</a><span class=\"tooltiptext\">Login to confirm checkout!</span>");
            $template->parseCurrentBlock();
        } else {

            $template->setCurrentBlock("NO_ORDER");
            $template->setVariable("NO_ORDER", "<h2 style='text-align: center; margin: auto; padding: 1vw;'>There are no items in the cart!</h2>");
            $template->parseCurrentBlock();

        }
    }

    $template->show();

    mysqli_close($db);
}

?>