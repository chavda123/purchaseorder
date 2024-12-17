<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once 'includes/connection.php';

if(!empty($_POST['supplier']) && !empty($_POST['products'])) {

    $stmt = $mysql->prepare("INSERT INTO orders (supplier_id, total) VALUES (?, ?)");

    $stmt->bind_param('is', $_POST['supplier'], $_POST['total']);    

    $stmt->execute();

    $order_id = $mysql->insert_id;

    if(!empty($order_id)) {
        $products = json_decode($_POST['products'], true);
        foreach($products as $product) {
            $stmt = $mysql->prepare("INSERT INTO order_product (order_id, product_id, qty, price) VALUES (?, ?, ?, ?)");

            $stmt->bind_param('iiis', $order_id, $product['id'], $product['qty'], $product['item_price']);    

            $stmt->execute();
        }
        $resultArray['order_id'] = $order_id;
    }
    
    $resultArray['success'] = "added order item";
} else {
    $resultArray['error'] = "No any items"; 
}

echo json_encode($resultArray);
exit;
?>