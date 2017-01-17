<?php

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Создаем название страницы
$title = 'Добавить пользователя';

// если форма добавления продукта была отправлена - вставляем продукт в базу.
if(!empty($_POST)){
    if(!$_POST['name'] || !$_POST['phone']){
        $error = 'Все поля обязательны для заполнения';
    }

    if(!isset($error)){
        $name = $_POST['name'];
        $family = $_POST['family'];
        $phone = $_POST['phone'];

        // Создаем массив значений для вставки в базу данных и превращаем его в строку, оборачивая переменные в кавычки
        $values[] = "'$name'";
        $values[] = "'$family'";
        $values[] = "'$phone'";
        $values = implode(', ', $values);

        $query = "INSERT INTO clients (name, family, phone) VALUES ($values)";

        $result = mysqli_query($link, $query);

        if($result){
            $success = 'Клиент успешно создан';
            unset($_POST);

            header('location: clientList.php');
        }else {
            $error = 'Вставка не удалась';
        }

    }
}

// Подключаем наш интерфейс
include "addClient.phtml";