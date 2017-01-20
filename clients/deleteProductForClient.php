<?php
include "../security/checkLogin.php";

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Создаем название страницы
$title = 'Удалить товар у клиента';


if(isset($_GET['product']) && isset($_GET['comment'])){
    $product = (int) $_GET['product'];
    $comment = (int) $_GET['comment'];

    if(!$product || !$comment){
        $error = 'Не указаны параметры для удаления';
    }

    $query = "DELETE FROM client_comment_join_product WHERE comment = $comment AND product = $product";

    $result = mysqli_query($link, $query);

    if(!$result){
        $error = 'Удаление не удалось';
    }

    if(!isset($error)){
        header('location: clientList.php');
    }
}

include 'deleteProductForClient.phtml';