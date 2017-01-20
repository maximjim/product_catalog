<?php
include "../security/checkLogin.php";

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Создаем название страницы
$title = 'Добавить товар';

$querySetup = 'SELECT * FROM setup WHERE id = 1';

$resultSetup = mysqli_query($link, $querySetup);

$setup = array();
while ($row = mysqli_fetch_assoc($resultSetup)) {
    $setup = $row;
    break;
};

// если форма добавления продукта была отправлена - вставляем продукт в базу.
if(!empty($_POST) ){

    // Берем данные из переданной нам формы.
    $artical = isset($_POST['artical']) ? $_POST['artical'] : null;
    $brand = isset($_POST['brand']) ? $_POST['brand'] : null;
    $name = isset($_POST['name']) ? $_POST['name'] : null;
    $price = isset($_POST['price']) ? $_POST['price'] : null;
    $count = isset($_POST['count']) ? $_POST['count'] : null;
    $provider = isset($_POST['provider']) ? $_POST['provider'] : null;
    $delivery = isset($_POST['delivery']) ? $_POST['delivery'] : null;
    $priceSell = isset($_POST['price_sell']) ? $_POST['price_sell'] : null;

    if(!preg_match('/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/', $_POST['delivery'])){
        $error = '<h4 style="color: red; text-align: center">Формат даты указан не верно.</h4>';
    }
    if(!preg_match("/^(\\w||\\d)+$/", $artical )){
        $error = '<h4 style="color: red; text-align: center">Артикул не должен содержать символов кроме букв и цифр.</h4>';
    }

    //Проверяем чтобы все поля были заполнены, если что не заполнено
    // и сообщаем об этом..
    if(!$artical || !$price || !$brand || !$count || !$provider || !$delivery || !$priceSell || !$name){
        $error =   '<h4 style="color: red; text-align: center">Не заполнены все поля</h4>';
    }

    // если нету ошибок вставляем в базу
    // иначе показываем ошибку
    if(!isset($error)) {


        // Создаем массив значений для вставки в базу данных и превращаем его в строку, оборачивая переменные в кавычки
        $values[] = "'$artical'";
        $values[] = "'$brand'";
        $values[] = "'$price'";
        $values[] = "'$name'";
        $values[] = $count;
        $values[] = "'$provider'";
        $values[] = "'$delivery'";
        $values[] = $priceSell;
        $values = implode(', ', $values);

        // Пишем SQL запрос для вставки в базу данных продукта.
        $query = "INSERT INTO product (artical, brand, price, name, count_product, provider, delivery_time, price_sell) VALUES ($values)";

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

