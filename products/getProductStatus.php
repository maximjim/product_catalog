<?php
// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

$query = "SELECT * FROM product_status";

$results = mysqli_query($link, $query);

$statuses = array();
while($row = mysqli_fetch_assoc($results)){
    $statuses[] = $row;
}

echo json_encode($statuses);