<?php

include "../security/checkLogin.php";

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Создаем название страницы
$title = 'Добавить приход';

if(!empty($_POST)){
    if(isset($_POST['text']) && !empty($_POST['text']) && isset($_POST['sum']) && !empty($_POST['sum'])){
        $text = $_POST['text'];
        $sum = $_POST['sum'];

        $date = new \DateTime();
        $date = $date->format('Y-m-d H:i:s');


        $date = "'$date'";
        $text = "'$text'";
        $query = "INSERT INTO clients_comments (comment, amount, createdAt, inCashier) VALUES ($text, $sum, $date, 1)";

        $result = mysqli_query($link, $query);
        if($result){
            header("location: /cashier/incomes.php");
        }else {
            $error = "Вставка не удалась";
        }
    }else {
        $error = " Все поля обязательны для заполнения";
    }
}


include "addIncomes.phtml";