<?php

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Создаем название страницы
$title = 'Изменение настроек СМС рассылки';

if(!empty($_POST)){

    if(!$_POST['host'] || !$_POST['port'] || !$_POST['login'] || !$_POST['password']){
        $error = "Все поля обязательные и не могут быть пустыми";
    }

    if(!isset($error)){
        $host = $_POST['host'];
        $port = $_POST['port'];
        $login = $_POST['login'];
        $password = $_POST['password'];

        $host = "'$host'";
        $port = "'$port'";
        $login = "'$login'";
        $password = "'$password'";

        $query = "UPDATE setup_sms SET
          host = $host,
          port = $port,
          login = $login,
          password = $password
          WHERE id = 1";

        $result = mysqli_query($link, $query);

        if($result){
            $success = "Настройки успешно обновлены";
        }else {
            $error = "Обновление не удалось";
        }
    }
}



$query = "SELECT * FROM setup_sms WHERE id = 1";

$result = mysqli_query($link, $query);
$setup = array();

while ($row = mysqli_fetch_assoc($result)) {
    $setup = $row;
    break;
};


// Подключаем наш интерфейс
include "changeSetupSMS.phtml";

