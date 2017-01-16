<?php

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Создаем название страницы
$title = 'Расходы';

$balance = include "balance.php";


$query = "SELECT * FROM consignments ORDER BY date DESC";

$consignments = array();
$results = mysqli_query($link, $query);

while ($row = mysqli_fetch_assoc($results)) {
    $consignments[] = $row;
};



// Подключаем наш интерфейс
include "expenses.phtml";