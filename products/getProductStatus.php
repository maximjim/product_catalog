<?php
// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

$query = "SELECT id AS value, name AS text FROM product_status";

$results = mysqli_query($link, $query);

$statuses = array();
while($row = mysqli_fetch_assoc($results)){
    $statuses[] = $row;
}

print json_encode($statuses);