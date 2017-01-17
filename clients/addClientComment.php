<?php

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Создаем название страницы
$title = 'Добавить заявку';

// Если форма была отправлена начинаем обработку формы
if(!empty($_POST)){
    if(!isset($_POST['client']) || !isset($_POST['comment']) || !$_POST['comment'] ){
        $error = 'Все поля обязательные для заполнения';
    }
    $client = $_POST['client'];
    if(!isset($error)){

        $querySearchClient = "SELECT * FROM clients WHERE id = $client";

        $result = mysqli_query($link, $querySearchClient);

        /* Выборка результатов запроса */
        $client = null;

        while ($row = mysqli_fetch_assoc($result)) {
            $client = $row['id'];
            break;
        };

        if (!$client) {
            $error = 'Клиент не найден';
        }
    }

    if(!isset($error)){
        $client = $_POST['client'];
        $comment = $_POST['comment'];

        $date = new \DateTime();
        $date = $date->format('Y-m-d h:i:s');

        // Создаем массив значений для вставки в базу данных и превращаем его в строку, оборачивая переменные в кавычки
        $values[] = "'$client'";
        $values[] = "'$comment'";
        $values[] = "'$date'";
        $values = implode(', ', $values);

        $query = "INSERT INTO clients_comments (client, comment, createdAt) VALUES ($values)";

        $result = mysqli_query($link, $query);

        if($result){
            $success = 'Заявка успешно добавлена';

            header("location: clientList.php?client=" . $client);
        } else {
            $error = 'Вставка не удалась';
        }
    }
}

if(!isset($error)) {

    $userId = isset($_GET['client']) ? $_GET['client'] : null;

    if (!$userId) {
        $userId = isset($_POST['client']) ? $_POST['client'] : null;
    }
    $userId = intval($userId);

    if (!$userId) {
        $error = 'Не указан клиент';
    }

    $querySearchClient = "SELECT * FROM clients WHERE id = $userId";

    $result = mysqli_query($link, $querySearchClient);

    /* Выборка результатов запроса */
    $client = null;

    while ($row = mysqli_fetch_assoc($result)) {
        $client = $row['id'];
        break;
    };

    if (!$client) {
        $error = 'Клиент не найден';
    }

}

if(!isset($error)){
    $queryComment = "SELECT comment FROM clients_comments WHERE client = $client";

    $results = mysqli_query($link, $queryComment);

    $comments = array();
    while ($row = mysqli_fetch_assoc($results)) {
        $comments[] = $row;
    };
};

// Подключаем наш интерфейс
include "addClientComment.phtml";