<?php
// Запускаем сессию
session_start();

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Создаем название страницы
$title = 'Добавить накладную';

// Берем список текущих товары из сессии
$currentProducts = isset($_SESSION['products']) ? $_SESSION['products'] : array();


// Если форма была отправлена начинаем обработку
if (!empty($_POST)) {
    if (!isset($_SESSION['products']) || empty($_SESSION['products'])) {
        $error = 'Вы не выбрали товаров для создания';

    } else {
        if(!preg_match('/[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/', $_POST['date'])){
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

                        // Соединяем нашу накладную с товарами.
                        $query = "INSERT INTO consignments_join_product (product, consignment) VALUES ($productId, $consignmentId)";
                        $result = mysqli_query($link, $query);

                        if(!$result){
                            mysqli_rollback($link);
                            $error = 'При вставке товаров произошла ошибка. Попробуйте еще раз';
                        }
                    }

                    //если все прошло без ошибок сохраняемся

                    if(!isset($error)){
                        // Сохраняем наши изменения в базе.
                        mysqli_commit($link);

                        // Очищаем наши товары из сессии.
                        $_SESSION['products'] = array();
                        $currentProducts = array();
                        unset($_POST);

                        // Говорим что все хорошо
                        $success = 'Накладная успешно создана.';
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