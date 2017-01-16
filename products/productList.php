<?php
// Создаем название страницы
$title = 'Товары';

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

$query = "SELECT * FROM product WHERE id NOT IN (
          (SELECT product FROM consignments_join_product GROUP BY id))";

$results = mysqli_query($link, $query);

/* Выборка результатов запроса */
$products = array();
while ($row = mysqli_fetch_assoc($results)) {
    $products[] = $row;
};

if(empty($products)){
    $emptyResult = 'Товаров не найдено';
}




// Подключаем наш интерфейс
include "productList.phtml";