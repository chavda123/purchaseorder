<?php
include_once 'includes/connection.php';

if(!empty($_GET['order'])) {
    $id = null;
    $supplier = null;
    $total = null;

    $sql = "SELECT o.id, s.`name` as supplier, o.total FROM orders as o INNER JOIN supplier as s ON s.id = o.supplier_id WHERE o.id = ?";

    $stmt = $mysql->prepare($sql);

    $stmt->bind_param('i', $_GET['order']);

    $stmt->execute();

    $stmt->store_result();	

    $resultArray = [];

    if($stmt->num_rows > 0) {

        $stmt->bind_result($id, $supplier, $total);
        
        $stmt->fetch();
    }

    $sql = "SELECT op.product_id, p.code, p.product_name, p.size, op.qty, op.price FROM order_product as op INNER JOIN products as p ON p.id = op.product_id WHERE op.order_id = ?";

    $stmt1 = $mysql->prepare($sql);

    $stmt1->bind_param('i', $id);

    $stmt1->execute();

    $stmt1->store_result();	

    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <link rel="icon" type="image/svg+xml" href="/vite.svg" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>View Invoice</title>
        <link rel="stylesheet" href="http://localhost/purchaseorder/src/Order.css">
    </head>
    <body>
    <table width=400>
        <thead>
        <tr>
            <td clospan="4">Order ID : <?php echo $id; ?></td>
            <td clospan="3">Supplier : <?php echo $supplier; ?></td>
        </tr>
        <tr>
            <th class='header'>Code</th>
            <th class='header'>Product Name</th>
            <th class='header'>Size</th>
            <th class='header'>Qty</th>
            <th class='header'>Cost Price With GST(18%)</th>
            <th class='header'>Total</th>
        </tr>
        </thead>
        <tbody>
        <?php 
            if($stmt1->num_rows > 0) {

                $stmt1->bind_result($product_id, $code, $product_name, $size, $qty, $price);

                while($stmt1->fetch()) {
                    ?>
                    <tr>
                        <td><?php echo $code; ?></td>
                        <td><?php echo $product_name; ?></td>
                        <td><?php echo $size; ?></td>
                        <td><?php echo $qty; ?></td>
                        <td><?php echo $price; ?></td>
                        <td><?php echo $price; ?></td>
                        </tr>
                    <?php
                }
            } ?>
        </tbody>
        <tfoot>
            <tr>
            <td colspan="4"></td>
            <td class='header'>Total</td>
            <td><?php echo $total; ?></td>
            </tr>
        </tfoot>
        </table>
        </body>
        </html>
<?php 
} else {
    echo "No order found!";
    exit;
}

?>
