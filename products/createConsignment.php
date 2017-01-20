<?php
// Запускаем сессию
session_start();

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Создаем название страницы
$title = 'Добавить накладную';

// Берем список текущих товары из сессии
$currentProducts = isset($_SESSION['products']) ? $_SESSION['products'] : array();

include "../setup/smsService.php";

function sendSmsCameProduct($link, $productId)
{

    $queryProduct = "SELECT * from product WHERE id = $productId";

    $resultProductValue = mysqli_query($link, $queryProduct);
    $productValue = mysqli_fetch_assoc($resultProductValue);
    $queryClient =
        "SELECT c.* FROM clients AS c WHERE id =
        (SELECT client FROM clients_comments WHERE id =
        (SELECT comment FROM client_comment_join_product WHERE product = $productId))";

    $resultClient = mysqli_query($link, $queryClient);
    $client = mysqli_fetch_assoc($resultClient);

    $querySetup = "SELECT * FROM setup_sms WHERE id = 1";
    $resultSetup = mysqli_query($link, $querySetup);
    $setup = mysqli_fetch_assoc($resultSetup);

    $productName = $productValue['name'];
    $host = $setup['host'];
    $port = $setup['port'];
    $login = $setup['login'];
    $password = $setup['password'];
    $phoneSent = $client['phone'];
    $text = "($productName) - поступило, тел. 79624003999";

    send("gate.prostor-sms.ru", 80, $login, $password,
        $phoneSent, $text);
}

// Если форма была отправлена начинаем обработку
if (!empty($_POST)) {
    if (!isset($_SESSION['products']) || empty($_SESSION['products'])) {
        $error = 'Вы не выбрали товаров для создания';

    } else {
        if (!preg_match('/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/', $_POST['date'])) {
            $error = 'формат даты указан не верно.';
        }
        if (empty($_POST['number']) || empty($_POST['amount'])) {
            $error = 'Все поля обязательны для заполнения.';

        } else {
            $number = $_POST['number'];
            $amount = $_POST['amount'];
            $date = $_POST['date'];

            // Создаем массив значений для вставки в базу данных и превращаем его в строку, оборачивая переменные в кавычки
            $values[] = "'$number'";
            $values[] = "'$amount'";
            $values[] = "'$date'";
            $values = implode(', ', $values);

            // Пишем SQL для вставки данных в базу.
            $query = "INSERT INTO consignments (number, amount, date) VALUES ($values)";

            mysqli_begin_transaction($link);
            $result = mysqli_query($link, $query);
            // Если вставка прошла успешно подвязываем этой накладной наши выбранные ранее продукты.
            if ($result) {

                // Ищем нашу ново-созданную накладную и получаем последний вставленный ID в базу
                $consignmentId = mysqli_insert_id($link);
                $products = $_SESSION['products'];
                $currentConsignment =
                $productsId = array();
                foreach ($products as $product) {
                    $productId = $product['id'];
                    $productsId[] = $product['id'];

                    // Соединяем нашу накладную с товарами.
                    $query = "INSERT INTO consignments_join_product (product, consignment) VALUES ($productId, $consignmentId)";
                    $result = mysqli_query($link, $query);

                    if (!$result) {
                        mysqli_rollback($link);
                        $error = 'При вставке товаров произошла ошибка. Попробуйте еще раз';
                    }
                }

                foreach ($productsId as $productToSms) {
                    sendSmsCameProduct($link, $productToSms);
                }

                $productsId = implode(', ', $productsId);
                $queryUpdateProduct = "UPDATE product SET status =
                        (SELECT id FROM product_status AS p WHERE p.key = 'came') WHERE id IN ($productsId)";

                mysqli_query($link, $queryUpdateProduct);

                //если все прошло без ошибок сохраняемся

                if (!isset($error)) {
                    // Сохраняем наши изменения в базе.
                    mysqli_commit($link);

                    // Очищаем наши товары из сессии.
                    $_SESSION['products'] = array();
                    $currentProducts = array();
                    unset($_POST);

                    // Говорим что все хорошо
                    $success = 'Накладная успешно создана.';
                }

                $queryComment = "SELECT c.*, s.key AS status_key FROM clients_comments AS c
            LEFT JOIN claim_status AS s ON c.status = s.id
            WHERE s.key NOT IN ('issued', 'came')";

                $results = mysqli_query($link, $queryComment);

                $comments = array();
                while ($row = mysqli_fetch_assoc($results)) {
                    $comments[] = $row;
                }

                foreach ($comments as $comment) {
                    $commentId = $comment['id'];

                    $query = "SELECT p.*, s.key AS status_key FROM product AS p
                           LEFT JOIN product_status AS s ON p.status = s.id
                           LEFT JOIN client_comment_join_product AS cp ON p.id = cp.product
                           WHERE cp.comment = $commentId";

                    $products = array();

                    $resultsProducts = mysqli_query($link, $query);

                    while ($row = mysqli_fetch_assoc($resultsProducts)) {
                        $products[] = $row;
                    }
                    $isCame = false;
                    if (!empty($products)) {
                        foreach ($products as $product) {
                            if ($product['status_key'] == 'came') {
                                $isCame = true;
                            } else {
                                $isCame = false;
                                break;
                            }
                        }
                    }

                    if ($isCame) {
                        $queryUpdateComment = "UPDATE clients_comments SET status = (SELECT id FROM claim_status AS s WHERE
                                          s.key = 'came') WHERE id = $commentId";
                        $resultUpdate = mysqli_query($link, $queryUpdateComment);

                        $clientQuery = "SELECT * FROM clients WHERE id =
                                          (SELECT client FROM clients_comments WHERE id = $commentId)";
                        $resultClient = mysqli_query($link, $clientQuery);
                        $client = mysqli_fetch_assoc($resultClient);

                        $phoneSent = $client['phone'];
                        $text = "Ваш заказ поступил, тел. 79624003999";

                        $querySetup = "SELECT * FROM setup_sms WHERE id = 1";
                        $resultSetup = mysqli_query($link, $querySetup);
                        $setup = mysqli_fetch_assoc($resultSetup);

                        $host = $setup['host'];
                        $port = $setup['port'];
                        $login = $setup['login'];
                        $password = $setup['password'];

                        $resSms = send("gate.prostor-sms.ru", 80, $login, $password,
                            $phoneSent, $text);
                    }

                }


            } else {
                mysqli_rollback($link);
                $error = 'При вставке произошла ошибка. Попробуйте еще раз';
            }
        }
    }
}


// Подключаем наш интерфейс
include "createConsignment.phtml";