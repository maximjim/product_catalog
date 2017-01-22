<?php

include "../security/checkLogin.php";

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

$status = $_POST['value'];
$product = $_POST['pk'];

$queryStatus = "select * from product_status WHERE id=$status";
$queryProduct = "SELECT * from product WHERE id = $product";

$resultProductValue = mysqli_query($link, $queryProduct);
$productValue = mysqli_fetch_assoc($resultProductValue);
$resultStatusValue = mysqli_query($link, $queryStatus);
$statusValue = mysqli_fetch_assoc($resultStatusValue);

if($statusValue['key'] == 'reject'){
    include "../setup/smsService.php";

    $queryClient =
        "SELECT c.* FROM clients AS c WHERE id =
        (SELECT client FROM clients_comments WHERE id =
        (SELECT comment FROM client_comment_join_product WHERE product = $product))";

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
    $text = "По пизиции ($productName) - отказ, тел. 79624003999";

    $resSms =  send("gate.prostor-sms.ru", 80, $login, $password,
        $phoneSent, $text);

    file_put_contents('sms.txt', $resSms);
}


$query = "UPDATE product SET status = $status WHERE id = $product";
mysqli_query($link, $query);

$commentQuery = "SELECT comment FROM client_comment_join_product WHERE product = $product";

$commentResult = mysqli_query($link, $commentQuery);
$comment = mysqli_fetch_assoc($commentResult);
$comment = $comment['comment'];

$queryTotalSum = "SELECT sum(price_sell * count_product) as totalSum FROM product AS p
                              WHERE p.id IN
                              ((SELECT product FROM client_comment_join_product AS cmp WHERE cmp.comment = $comment))
                               AND p.id IN ((SELECT id FROM product WHERE status != (SELECT id FROM product_status AS s WHERE s.key = 'return')))";

$totalSumResult = mysqli_query($link, $queryTotalSum);
$totalSum = mysqli_fetch_assoc($totalSumResult);
$totalSum = $totalSum['totalSum'];
$totalSum = number_format($totalSum, 2);
echo json_encode(['comment' => $comment, 'totalSum' => $totalSum]);