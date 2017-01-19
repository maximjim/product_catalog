<?php

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

require_once "../params/numberToString.php";

if (isset($_GET['comment'])) {
    $commentId = $_GET['comment'];
}
if (isset($commentId)) {
    $queryComment = "SELECT * FROM clients_comments WHERE id = $commentId ";

    $resultComment = mysqli_query($link, $queryComment);

    $comment = array();
    while ($row = mysqli_fetch_assoc($resultComment)) {
        $comment = $row;
    };

    if ($comment) {
        $createdAt = new \DateTime($comment['createdAt']);
        $createdAt = $createdAt->format('d\\\m\\\Y');


        $clientId = $comment['client'];
        $queryClient = "SELECT * FROM clients WHERE id = $clientId";

        $resultClient = mysqli_query($link, $queryClient);
        $client = array();

        while ($row = mysqli_fetch_assoc($resultClient)) {
            $client = $row;
        };

        $queryProduct = "SELECT * FROM product WHERE id IN
          ((SELECT product FROM client_comment_join_product WHERE comment = $commentId))";

        $products = array();

        $resultProducts = mysqli_query($link, $queryProduct);

        while ($row = mysqli_fetch_assoc($resultProducts)) {
            $products[] = $row;
        };


// Получаем настройки
        $querySetup = "SELECT * FROM setup WHERE id = 1";

        $resultSetup = mysqli_query($link, $querySetup);
        $setup = array();
        while ($row = mysqli_fetch_assoc($resultSetup)) {
            $setup = $row;
        };
    }
}

// Подключаем наш интерфейс
include "printComment.phtml";