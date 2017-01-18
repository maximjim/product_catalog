<?php
// Подключаем файл параметров подключения к базе данных
$DBparams = include "config.php";

// Подключаемся к базе данных
$link = mysqli_connect($DBparams['host'], $DBparams['user'], $DBparams['password'], $DBparams['name']);

mysqli_set_charset($link, 'utf8' );

// Если по какой то причине не удалось подключиться показываем это
if (!$link) {
    echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
    echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}



// Возвращаем подключение к БД для дальнейшего его использования
return $link;