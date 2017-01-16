<?php
// Запускаем сессию
session_start();

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Создаем название страницы
$title = 'Добавить товар клиенту';


// если форма поиска товара была отправлена и она не пуста ищем товар по артикулу
if (isset($_POST['search']) && !empty($_POST['search'])) {

    // пишем запрос для подключения поиска товара по артикулу
    $search = $_POST['search'];

    // Берем список текущих товары из сессии и получаем список ИД
    $currentProducts = isset($_SESSION['clientProducts']) ? $_SESSION['clientProducts'] : array();
    $currentProductsId = array();

    foreach ($currentProducts as $key => $currentProduct) {
        $currentProductsId[] = $currentProduct['id'];

    }

    // Пеереводим ID товаров из массива в строку
    $currentProductsId = implode(', ', $currentProductsId);

    $query = "SELECT * FROM product WHERE artical = '" . $search . "' AND id NOT IN ((SELECT product FROM client_comment_join_product GROUP BY id))";
    // Если уже у нас есть товары в сессии для создания накладной то исключаем их из будущего поиска
    if ($currentProductsId) {
        $query .= ' AND id NOT IN (' . $currentProductsId . ')';
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
if (isset($_POST['action']) && isset($_POST['productId'])) {
    $productId = $_POST['productId'];
    // Берем список текущих товары из сессии и получаем список ИД
    $currentProducts = isset($_SESSION['clientProducts']) ? $_SESSION['clientProducts'] : array();

    if ($_POST['action'] === 'add') {

        $productInArray = false;

        // Проверяем существует ли этот товар уже в сессии
        // Если да то ничего не делаем, если нет то добавляем
        foreach ($currentProducts as $currentProduct) {
            if ($currentProduct['id'] == $productId) {
                $productInArray = true;
                break;
            }
        }

        if (!$productInArray) {
            //ищем товар в базе, если находим добавляем его в сессию, если не находим ничего не делаем.

            $query = "SELECT * FROM product where id = $productId";
            $results = mysqli_query($link, $query);
            while ($row = mysqli_fetch_assoc($results)) {
                $product = $row;
                break;
            };

            if (isset($product)) {
                $_SESSION['clientProducts'][] = $product;
            }
        }
    }
    if ($_POST['action'] === 'delete') {
        // ищем товар в сессии и удаляем его из сессии

        if (!empty($_SESSION['clientProducts'])) {
            foreach ($_SESSION['clientProducts'] as $key => $product) {
                if ($productId == $product['id']) {
                    unset($_SESSION['clientProducts'][$key]);
                }
            }
        }
    }
    // Очищаем этот продукт чтобы он скрылся из списка поиска
    unset($product);

}

$client = isset($_GET['client']) ? $_GET['client'] : null;
$comment = isset($_GET['comment']) ? $_GET['comment'] : null;

if ($comment && $client) {
    $query = "SELECT * FROM clients_comments WHERE id = $comment AND client = $client";

    $result = mysqli_query($link, $query);

    $isComment = null;
    while ($row = mysqli_fetch_assoc($result)) {
        $isComment = $row;
        break;
    };

    if (!$isComment) {
        $error = 'Комментарий этого клиента не существует';
    } else {

        if (!isset($error) && isset($_GET['action']) && $_GET['action'] === 'save_product') {
            $products = isset($_SESSION['clientProducts']) ? $_SESSION['clientProducts'] : array();

            if (!empty($products)) {
                foreach ($products as $product) {

                    mysqli_begin_transaction($link);
                    $productIdToSave = $product['id'];

                    $query = "INSERT INTO client_comment_join_product(product, comment) VALUES ($productIdToSave, $comment)";

                    $resultInsert = mysqli_query($link, $query);
                    if (!$resultInsert) {
                        $error = 'Вставка не удалась.';
                        mysqli_rollback($link);
                    }

                }
                if (!isset($error)) {
                    $success = 'Товары успешно добавлены клиенту.';
                    mysqli_commit($link);
                    unset($_SESSION['clientProducts']);
                    unset($product);
                    header('location: clientList.php');
                }
            } else {
                $error = "Вы не выбрали товаров для добавления";
            }

        }

    }
}


// Подключаем наш интерфейс
include "addProductToClient.phtml";