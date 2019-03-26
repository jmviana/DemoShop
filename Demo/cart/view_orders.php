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
    $template->loadTemplatefile("view_orders.html", true, true);

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
            for ($i = 0; $i < $numrows; $i++) {
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

        $getOrdersStatus1_query = "SELECT * FROM orders WHERE status=1;";
        if (!($result = @ mysqli_query($db, $getOrdersStatus1_query)))
            showerror($db);

        $numrows = mysqli_num_rows($result);
        if ($numrows > 0) {
            $template->setCurrentBlock("ORDERS_INFO");
            $template->setVariable("ORDERS_TITLE", "
                <div class=\"checkout-head\">
                    <span class=\"checkout-title\">PLACED ORDERS</span>
                </div>");
            $template->parseCurrentBlock();

            for ($i=0; $i<$numrows; $i++) {

                $template->setCurrentBlock("ORDERS");

                $tuple = mysqli_fetch_array($result);
                $orderId = $tuple['id'];
                $created_at = $tuple['created_at'];

                $getOrderInfo_query = "SELECT * FROM order_items AS oi, products AS p, orders AS o
                                    WHERE p.id = oi.product_id AND o.id = oi.order_id AND o.id = '$orderId';";
                if (!($result2 = @ mysqli_query($db, $getOrderInfo_query)))
                    showerror($db);

                $numrows2 = mysqli_num_rows($result2);
                $order_items = "";
                $total_quantity = 0;
                $total_price = 0;

                for ($j=0; $j<$numrows2; $j++) {

                    $tuple2 = mysqli_fetch_array($result2);
                    $price = $tuple2['new_price']!=null ? $tuple2['new_price'] : $tuple2['price'];
                    $total_quantity += $tuple2['quantity'];
                    $total_price += $price * $tuple2['quantity'];
                    $order_items .= "
                        <tr>
                            <td><img src=\"../img/".$tuple2['image']."\"></td>
                            <td><span>".$tuple2['name']."</span></td>
                            <td><span>".$price."€</span></td>
                            <td><span>".$tuple2['quantity']."</span></td>
                        </tr>";
                }

                $template->setVariable("ORDERS", "Order created at: ".$created_at."
                <table style=\"border-collapse: collapse; margin-bottom: 40px; margin-top: 10px;\">
                    <thead>
                        <tr>
                            <th style=\"border-radius: 0\"><span>Product Image</span></th>
                            <th><span>Product Name</span></th>
                            <th><span>Product Price</span></th>
                            <th style=\"border-radius: 0\"><span>Quantity</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        ".$order_items."
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total Price (".$total_quantity." item(s)): </th>
                            <th></th>
                            <th></th>
                            <th>".$total_price."€</th>
                         </tr> 
                    </tfoot>
                </table>");
                $template->parseCurrentBlock();
            }

        } else {
            $template->setCurrentBlock("ORDERS_INFO");
            $template->setVariable("NO_ORDERS", "<h2 style=\"text-align: center; margin: auto; padding: 1vw;\">There are no placed orders!</h2>");
            $template->parseCurrentBlock();
        }
    }

    $template->show();
    mysqli_close($db);
}

?>