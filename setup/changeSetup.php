<?php

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Устанавливаем кодировку русских символов для этой страницы
mysqli_query($link, "SET NAMES 'utf8'");
mysqli_query($link, "SET CHARACTER SET utf8 ");

// Создаем название страницы
$title = 'Изменение настроек';

if(!empty($_POST)){

    if(!$_POST['face'] || !$_POST['text'] || !$_POST['address']){
        $error = "Все поля обязательные и не могут быть пустыми";
    }

    if(!isset($error)){
        $face = $_POST['face'];
        $text = $_POST['text'];
        $address = $_POST['address'];

        $face = "'$face'";
        $address = "'$address'";
        $text = "'$text'";

        $query = "UPDATE setup SET face = $face, text = $text, address = $address WHERE id = 1";

        $result = mysqli_query($link, $query);

        if($result){
            $success = "Настройки успешно обновлены";
        }else {
            $error = "Обновление не удалось";
        }
    }
}



$query = "SELECT * FROM setup WHERE id = 1";

$result = mysqli_query($link, $query);
$setup = array();

while ($row = mysqli_fetch_assoc($result)) {
    $setup = $row;
    break;
};


// Подключаем наш интерфейс
include "changeSetup.phtml";

