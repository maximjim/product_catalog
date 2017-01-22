<?php
include "../security/checkLogin.php";

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';


$productId = $_POST['pk'];
$value = $_POST['value'];

$query = "UPDATE product SET price = $value, status = (SELECT id FROM product_status as s WHERE s.key = 'error_price') WHERE id = $productId";

$result = mysqli_query($link, $query);

if($result){
    foreach($_SESSION['products'] as $key => $product){
        if($product['id'] == $productId){
            $_SESSION['products'][$key]['price'] = $value;
            $_SESSION['products'][$key]['status_name'] = "Ошибка цены";
        }
    }

}

echo json_encode($result);
