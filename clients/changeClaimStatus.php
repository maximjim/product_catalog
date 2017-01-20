<?php
// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

$status = $_POST['value'];
$comment = $_POST['pk'];


$queryStatus = "select * from claim_status WHERE id=$status";
$resultStatusValue = mysqli_query($link, $queryStatus);
$statusValue = mysqli_fetch_assoc($resultStatusValue);

if ($statusValue['key'] == 'came' || $statusValue['key'] == 'reject' || $statusValue['key'] == 'processing') {

    include "../setup/smsService.php";

    $clientQuery = "SELECT * FROM clients WHERE id =
                  (SELECT client FROM clients_comments WHERE id = $comment)";
    $resultClient = mysqli_query($link, $clientQuery);
    $client = mysqli_fetch_assoc($resultClient);

    $querySetup = "SELECT * FROM setup_sms WHERE id = 1";
    $resultSetup = mysqli_query($link, $querySetup);
    $setup = mysqli_fetch_assoc($resultSetup);

    $host = $setup['host'];
    $port = $setup['port'];
    $login = $setup['login'];
    $password = $setup['password'];
    $phoneSent = $client['phone'];

    if ($statusValue['key'] == 'reject') {
        $text = "По вашему заказу - отказ, тел. 79624003999";

        $resSms = send("gate.prostor-sms.ru", 80, $login, $password,
            $phoneSent, $text);

    } elseif ($statusValue['key'] == 'processing') {

        $queryProducts = "SELECT * FROM product WHERE id IN
          ((SELECT product FROM client_comment_join_product WHERE comment = $comment))";
        $resultProducts = mysqli_query($link, $queryProducts);

        $products = array();

        while ($row = mysqli_fetch_assoc($resultProducts)) {
            $products[] = $row;
        }

        if (!empty($products)) {
            $text = "Заявка обработана - ";
            foreach ($products as $product) {
                $text .= $product['name'] . "(цена: " . $product['price_sell'] * $product['count_product'] . "),";
            }
            $text .= " тел. 79624003999";
            $resSms = send("gate.prostor-sms.ru", 80, $login, $password,
                $phoneSent, $text);
        }
    }
}
$query = "UPDATE clients_comments SET status = $status WHERE id = $comment";
mysqli_query($link, $query);


echo json_encode(true);