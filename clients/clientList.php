<?php
include "../security/checkLogin.php";

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

// Создаем название страницы
$title = 'Клиенты';

if(isset($_GET['client'])){
    $editClientId = $_GET['client'];
}

if(isset($editClientId)){
    $clients = array();

    $query = "SELECT * FROM clients WHERE id = $editClientId";

    $result = mysqli_query($link, $query);

    /* Выборка результатов запроса */
    while ($row = mysqli_fetch_assoc($result)) {
        $clients[] = $row;
        break;
    };

    $query = "SELECT * FROM clients WHERE id != $editClientId ORDER BY id DESC";

    $results = mysqli_query($link, $query);

    /* Выборка результатов запроса */
    while ($row = mysqli_fetch_assoc($results)) {
        $clients[] = $row;
    };
}else{
    $clients = array();
    $query = 'SELECT * FROM clients ORDER BY id DESC';

    $results = mysqli_query($link, $query);

    /* Выборка результатов запроса */
    while ($row = mysqli_fetch_assoc($results)) {
        $clients[] = $row;
    };
}




if (empty($clients)) {
    $emptyResult = 'Клиентов не найдено';
} else {

    $query = 'SELECT s.*, cs.name AS status_name, cs.key AS status_key FROM clients_comments AS s
        LEFT JOIN claim_status AS cs ON s.status = cs.id
        WHERE s.client = ';
    foreach ($clients as $key => $client) {
        $queryToResult = $query . $client['id'];

        $clients[$key]['comments'] = array();
        $results = mysqli_query($link, $queryToResult);

        $comments = array();

        while ($row = mysqli_fetch_assoc($results)) {
            $clients[$key]['comments'][] = $row;
        };

        if (!empty($clients[$key]['comments'])) {

            foreach ($clients[$key]['comments'] as $keyComment => $comment) {

                $clients[$key]['comments'][$keyComment]['products'] = array();
                $currentClient = $client['id'];

                if(!isset($clients[$key]['comments']['id'])){}
                $currentComment = $clients[$key]['comments'][$keyComment]['id'];

                $queryTotalSum = "SELECT sum(price_sell * count_product) as totalSum FROM product AS p
                              WHERE p.id IN
                              ((SELECT product FROM client_comment_join_product AS cmp WHERE cmp.comment = $currentComment)) ";

                $resultTotalSum = mysqli_query($link, $queryTotalSum);

                while ($row = mysqli_fetch_assoc($resultTotalSum)) {

                    $clients[$key]['comments'][$keyComment] = array_merge($clients[$key]['comments'][$keyComment], $row);

                    break;
                };

                $queryProduct = "SELECT p.*, s.name AS status_name
            FROM product as p
            LEFT JOIN product_status AS s ON p.status = s.id
            WHERE p.id IN
            ((SELECT product FROM client_comment_join_product WHERE comment IN
            ((SELECT id FROM clients_comments WHERE client = $currentClient AND id = $currentComment)) ))";

                $results = mysqli_query($link, $queryProduct);

                while ($row = mysqli_fetch_assoc($results)) {
                    $clients[$key]['comments'][$keyComment]['products'][] = $row;
                };

            }
        }


    }


}

// Подключаем наш интерфейс
include "clientList.phtml";