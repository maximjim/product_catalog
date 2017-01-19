<?php
// Запускаем сессию
session_start();

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Создаем название страницы
$title = 'Добавить накладную';

// если форма поиска товара была отправлена и она не пуста ищем товар по артикулу
if (isset($_POST['search']) && !empty($_POST['search'])) {

    // пишем запрос для подключения поиска товара по артикулу
    $search = $_POST['search'];

    // Берем список текущих товары из сессии и получаем список ИД
    $currentProducts = isset($_SESSION['products']) ? $_SESSION['products'] : array();
    $currentProductsId = array();

    foreach ($currentProducts as $key => $currentProduct) {
        $currentProductsId[] = $currentProduct['id'];

    }

    // Пеереводим ID товаров из массива в строку
    $currentProductsId = implode(', ', $currentProductsId);

    $query = "SELECT p.*, s.name AS status_name FROM product AS p
            LEFT JOIN product_status AS s ON p.status = s.id
            WHERE p.artical = '" . $search . "'
            AND p.id NOT IN ((SELECT product FROM consignments_join_product GROUP BY id))
            AND s.key = 'ordered'";


    // Если уже у нас есть товары в сессии для создания накладной то исключаем их из будущего поиска
    if ($currentProductsId) {
        $query .= ' AND p.id NOT IN (' . $currentProductsId . ')';
    }

    $results = mysqli_query($link, $query);
    /* Выборка результатов запроса */
    while ($row = mysqli_fetch_assoc($results)) {
        $product = $row;
        break;
    };

    if (!isset($product)) {
        // если товаров не было найдено создаем сообщение об этом
        $emptyResult = 'Товар не найден';
    }


}


// Добавляем или удаляем товар из сессии
if(isset($_POST['action']) && isset($_POST['productId'])){
    $productId = $_POST['productId'];
    // Берем список текущих товары из сессии и получаем список ИД
    $currentProducts = isset($_SESSION['products']) ? $_SESSION['products'] : array();

    if($_POST['action'] === 'add'){

        $productInArray = false;

        // Проверяем существует ли этот товар уже в сессии
        // Если да то ничего не делаем, если нет то добавляем
        foreach($currentProducts as $currentProduct){
            if($currentProduct['id'] == $productId){
                $productInArray = true;
                break;
            }
        }

        if(!$productInArray){
            //ищем товар в базе, если находим добавляем его в сессию, если не находим ничего не делаем.

            $query = "SELECT p.*, s.name AS status_name FROM product AS p
            LEFT JOIN product_status AS s ON p.status = s.id
            WHERE p.id = $productId";

            $results = mysqli_query($link, $query);
            while ($row = mysqli_fetch_assoc($results)) {
                $product = $row;
                break;
            };

            if(isset($product)){
                $_SESSION['products'][] = $product;
            }
        }
    }
    if($_POST['action'] === 'delete'){
        // ищем товар в сессии и удаляем его из сессии

        if(!empty($_SESSION['products'])){
            foreach($_SESSION['products'] as $key => $product){
                if($productId == $product['id']){
                    unset($_SESSION['products'][$key]);
                }
            }
        }
    }
    // Очищаем этот продукт чтобы он скрылся из списка поиска
    unset($product);

}

// Подключаем наш интерфейс
include "addGroupProduct.phtml";