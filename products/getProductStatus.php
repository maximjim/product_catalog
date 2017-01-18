<?php
// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';
mysqli_query($link, "SET NAMES 'utf8'");
mysqli_query($link, "SET CHARACTER SET utf8 ");

$query = "SELECT * FROM product_status";

$results = mysqli_query($link, $query);

$statuses = array();
while($row = mysqli_fetch_assoc($results)){
    $statuses[] = $row;
}

echo json_encode($statuses, true);