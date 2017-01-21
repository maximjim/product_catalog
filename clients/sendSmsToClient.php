<?php
include "../security/checkLogin.php";

$title = "Отправить смс клиенту";

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';


if (isset($_GET['client']) && empty($_POST)) {
    $client = $_GET['client'];

    $queryClient = "SELECT * FROM clients WHERE id = $client";
    $resultClient = mysqli_query($link, $queryClient);
    $clientFromDB = mysqli_fetch_assoc($resultClient);
    if (!$clientFromDB) {
        $emptyClient = "Клиент не найден";
    }
}else {
    if(!isset($_POST['client'])){
        $emptyClient = "Клиент не найден";
    }
}

if (!empty($_POST)) {

    $client = $_POST['client'];
    $text = $_POST['text'];

    if (!$text) {
        $error = "Не указан текст смс-сообщения";
    }

    if (!isset($error)) {
        include "../setup/smsService.php";

        $queryClient = "SELECT * FROM clients WHERE id = $client";
        $resultClient = mysqli_query($link, $queryClient);
        $clientFromDB = mysqli_fetch_assoc($resultClient);

        $querySetup = "SELECT * FROM setup_sms WHERE id = 1";
        $resultSetup = mysqli_query($link, $querySetup);
        $setup = mysqli_fetch_assoc($resultSetup);

        $host = $setup['host'];
        $port = $setup['port'];
        $login = $setup['login'];
        $password = $setup['password'];
        $phoneSent = $clientFromDB['phone'];

        $resSms = send("gate.prostor-sms.ru", 80, $login, $password,
            $phoneSent, $text);

        $success = "Смс успешно отправлено";

        unset($_POST);
    }
}

// Подключаем наш интерфейс
include "sendSmsToClient.phtml";