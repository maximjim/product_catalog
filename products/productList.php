<?php
// Создаем название страницы
$title = 'Товары';

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

$query = "SELECT p.* FROM product AS p
          WHERE p.id NOT IN (
          (SELECT product FROM consignments_join_product GROUP BY id))";

$results = mysqli_query($link, $query);

/* Выборка результатов запроса */
$products = array();
while ($row = mysqli_fetch_assoc($results)) {
    $products[] = $row;
};

if (empty($products)) {
    $emptyResult = 'Товаров не найдено';
} else {
    foreach ($products as $key => $product) {
        $statusId = $product['status'];
        $query = "SELECT name FROM product_status WHERE id = $statusId";

        $results = mysqli_query($link, $query);

        while ($row = mysqli_fetch_assoc($results)) {
            $products[$key]['status_name'] = $row['name'];
        };
    }
}


// Подключаем наш интерфейс
include "productList.phtml";