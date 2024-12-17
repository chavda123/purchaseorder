<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once 'includes/connection.php';

$id = null;
$name = null;
$sql = "SELECT id, `name` FROM supplier";

$stmt = $mysql->prepare($sql);

$stmt->execute();

$stmt->store_result();	

$resultArray = [];

if($stmt->num_rows > 0) {

    $stmt->bind_result($id, $name);
    
    while($stmt->fetch()) {
        $innerArray = [];
        $innerArray['id'] = $id;
        $innerArray['name'] = $name;
        $resultArray[] = $innerArray;
    }
}

echo json_encode($resultArray);
exit;
?>