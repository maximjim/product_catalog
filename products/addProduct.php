<?php

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Создаем название страницы
$title = 'Добавить товар';

// если форма добавления продукта была отправлена - вставляем продукт в базу.
if(!empty($_POST) ){

    // Берем данные из переданной нам формы.
    $artical = isset($_POST['artical']) ? $_POST['artical'] : null;
    $brand = isset($_POST['brand']) ? $_POST['brand'] : null;
    $price = isset($_POST['price']) ? $_POST['price'] : null;
    $count = isset($_POST['count']) ? $_POST['count'] : null;
    $provider = isset($_POST['provider']) ? $_POST['provider'] : null;


    //Проверяем чтобы все поля были заполнены, если что не заполнено
    // и сообщаем об этом..
    if(!$artical || !$price || !$brand || !$count || !$provider){
        $error =   '<h4 style="color: red; text-align: center">Не заполнены все поля</h4>';
    }

    // если нету ошибок вставляем в базу
    // иначе показываем ошибку
    if(!isset($error)) {


        // Создаем массив значений для вставки в базу данных и превращаем его в строку, оборачивая переменные в кавычки
        $values[] = "'$artical'";
        $values[] = "'$brand'";
        $values[] = "'$price'";
        $values[] = $count;
        $values[] = "'$provider'";
        $values = implode(', ', $values);

        // Пишем SQL запрос для вставки в базу данных продукта.
        $query = "INSERT INTO product (artical, brand, price, count_product, provider) VALUES ($values)";

        // Вставляем наш продукт в базу данных
        $result = mysqli_query($link, $query);

        //После вставки очищаем наш POST
        unset($_POST);

        // Если продукт добавлен говорим это и если не добавлен говорим это
        if ($result) {
            $success = '<h4 style="color: green; text-align: center">Продукт успешно добавлен</h4>';
        } else {
            $error = '<h4 style="color: red; text-align: center">Вставка не удалась</h4>';
        }

    }
}

// Подключаем наш интерфейс
include "addProduct.phtml";

