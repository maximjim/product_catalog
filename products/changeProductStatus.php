<?php
// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

$status = $_POST['value'];
$product = $_POST['pk'];

$query = "UPDATE product SET status = $status WHERE id = $product";
mysqli_query($link, $query);


echo json_encode(true);