<?php

include "../security/checkLogin.php";

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Создаем название страницы
$title = 'Приходы';

$balance = include "balance.php";

$query = "SELECT * FROM clients_comments ORDER BY createdAt DESC";

$comments = array();
$results = mysqli_query($link, $query);

while ($row = mysqli_fetch_assoc($results)) {
    $comments[] = $row;
};



// Подключаем наш интерфейс
include "incomes.phtml";