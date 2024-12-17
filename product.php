<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once 'includes/connection.php';

$id = null;
$code = null;
$product_name = null;
$size = null;
$price = null;

$sql_where = "1 = 1";
if(!empty($_REQUEST['id'])) {
    $sql_where .= " AND id = ?";
}

$sql = "SELECT id, `code`, `product_name`, `size`, price FROM products WHERE ".$sql_where;

$stmt = $mysql->prepare($sql);

if(!empty($_REQUEST['id'])) {
    $stmt->bind_param('i', $_REQUEST['id']);
}

$stmt->execute();

$stmt->store_result();	

$resultArray = [];

if($stmt->num_rows > 0) {

    $stmt->bind_result($id, $code, $product_name, $size, $price);
    
    while($stmt->fetch()) {
        $innerArray = [];
        $innerArray['id'] = $id;
        $innerArray['code'] = $code;
        $innerArray['product_name'] = $product_name;
        $innerArray['size'] = $size;
        $innerArray['price'] = $price;
        $resultArray[] = $innerArray;
    }
}
echo json_encode($resultArray);
exit;
?>