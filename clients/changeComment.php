<?php

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Создаем название страницы
$title = 'Изменить комментарий';


$comment = null;
if(isset($_GET['comment'])){
    $commentId = $_GET['comment'];

    $query = "SELECT * FROM clients_comments WHERE id = $commentId";

    $result = mysqli_query($link, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $comment = $row;
        break;
    };
} else {
    $error = 'Комментарий не указан';
}

if(!$comment){
    $error = 'Комментарий не найден';
}

if(!empty($_POST)){

    if(!$_POST['comment'] || !$_POST['id']){
        $error = 'Все поля обязательны для заполнения';
    }

    if(!isset($error)){
        $comment = $_POST['comment'];
        $comment = "'$comment'";
        $amount = intval($_POST['amount']);
        $id = $_POST['id'];

        $query = "UPDATE clients_comments SET comment = $comment, amount = $amount WHERE id = $id";

        $result = mysqli_query($link, $query);

        if($result){
            header('location: clientList.php');
        } else {
            $error = 'Не удалось изменить комментарий';
        }
    }
}





// Подключаем наш интерфейс
include "changeComment.phtml";