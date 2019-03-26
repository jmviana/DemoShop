<?php
require_once '../db.php';
require_once  "../HTML/Template/PEAR.php";
require_once "../HTML/Template/IT.php";
session_start(); ?>
<style>
    <?php include 'products.css'; ?>
</style>

<?php

    $db = dbconnect($hostname, $db_name, $db_user, $db_passwd);
    if ($db) {

        $product_id = $_GET['product_id'];
        if ($product_id != null) {

            $template = new HTML_Template_IT('.');

            $template->loadTemplatefile('products.html', true, true);

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

            $template->setCurrentBlock("MENU");
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
                $template->setVariable("MENU1", "<a href=\"../cart/view_orders.php\">View Orders</a>");
                $template->setVariable("MENU2", "<a href=\"../login/logout_action.php\">Logout</a>");
                $template->setVariable("MENU3", "<div>".$_SESSION['name']."</div>");
            } else {
                $total_quantity = 0;

                if (isset($_SESSION['cart'])) {
                    $cart = $_SESSION['cart'];
                    $total_quantity = sizeof($cart);
                }

                $template->setVariable("CART", $total_quantity);
                $template->setVariable("MENU1", "<a href=\"../login/login.php\">Login</a>");
                $template->setVariable("MENU2", "<a href=\"../register/register.php\" class=\"button highlight\">Register</a>");
            }
            $template->parseCurrentBlock();

            $query = "SELECT * FROM products WHERE id='$product_id'";

            // executes the query
            if (!($result = @ mysqli_query($db, $query)))
                showerror($db);

            $numrows  = mysqli_num_rows($result);
            for($i=0; $i<$numrows; $i++) {
                $tuple = mysqli_fetch_array($result);

                $template->setCurrentBlock("PRODUCT");
                $template->setVariable("PRODUCT_IMAGE", $tuple['image']);
                $template->setVariable("PRODUCT_ID", $product_id);
                $template->setVariable("PRODUCT_NAME", $tuple['name']);
                if ($tuple['new_price'] == null) {
                    $template->setVariable("PRODUCT_PRICE", $tuple['price'] . "€");
                } else {
                    $template->setVariable("PRODUCT_OLD_PRICE", $tuple['price'] . "€");
                    $template->setVariable("PRODUCT_PRICE", $tuple['new_price'] . "€");
                }
                $description = $tuple['description'] != null ? $tuple['description'] : "No description";
                $template->setVariable("PRODUCT_DESCRIPTION", $description);
                $template->setVariable("PRODUCT_BRAND", $tuple['brand']);
                $template->setVariable("PRODUCT_WARRANTY", $tuple['warranty']);
                $template->parseCurrentBlock();
            }

            $template->show();
        }
    }

?>