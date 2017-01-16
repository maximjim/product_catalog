<?php
// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Создаем название страницы
$title = 'Накладные';

$queryConsignments = 'SELECT * FROM consignments';

$resultsConsignments = mysqli_query($link, $queryConsignments);
/* Выборка результатов запроса */
$consignments = array();
while ($row = mysqli_fetch_assoc($resultsConsignments)) {
    $consignments[] = $row;
};

if (empty($consignments)) {
    $emptyResult = 'Накладных не найдено';
} else {

    $queryProduct = "SELECT * FROM product as p LEFT JOIN consignments_join_product AS c ON c.product = p.id WHERE c.consignment = ";
    foreach ($consignments as $key => $consignment) {
        $consignments[$key]['products'] = array();
        $queryProductToResult = $queryProduct . $consignment['id'];
        $products = mysqli_query($link, $queryProductToResult);

        while ($row = mysqli_fetch_assoc($products)) {
            $consignments[$key]['products'][] = $row;
        };

    }


}

// Подключаем наш интерфейс
include "consignmentList.phtml";