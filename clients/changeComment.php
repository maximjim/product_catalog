<?php

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Создаем название страницы
$title = 'Изменить комментарий';


$querySetup = 'SELECT * FROM setup WHERE id = 1';

$resultSetup = mysqli_query($link, $querySetup);

$setup = array();
while ($row = mysqli_fetch_assoc($resultSetup)) {
    $setup = $row;
    break;
};

$comment = null;
if(isset($_GET['comment'])){
    $commentId = $_GET['comment'];

    $query = "SELECT * FROM clients_comments WHERE id = $commentId";

    $result = mysqli_query($link, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $comment = $row;
        break;
    };

    $queryTotalSum = "SELECT sum(price * count_product) as totalSum FROM product AS p
                              WHERE p.id IN
                              ((SELECT product FROM client_comment_join_product AS cmp WHERE cmp.comment = $commentId)) ";

    $resultTotalSum = mysqli_query($link, $queryTotalSum);
    if(!empty($comment)){
        while ($row = mysqli_fetch_assoc($resultTotalSum)) {
            $comment['totalSum'] = $row['totalSum'];
            break;
        };
    }
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

        if($amount > 0){
            $queryUpdateProduct = "UPDATE product SET status = (SELECT id FROM product_status AS p WHERE p.key = 'treated') WHERE id IN
              ((SELECT product FROM client_comment_join_product WHERE comment = $commentId))";

            mysqli_query($link, $queryUpdateProduct);
        }

        if($result){
            header('location: clientList.php');
        } else {
            $error = 'Не удалось изменить комментарий';
        }
    }


}





// Подключаем наш интерфейс
include "changeComment.phtml";