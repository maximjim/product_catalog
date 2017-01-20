<?php
include "../security/checkLogin.php";

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

$queryParams = "SELECT order_time FROM setup WHERE id =1";

$results = mysqli_query($link, $queryParams);

while ($row = mysqli_fetch_assoc($results)) {
    $orderTime = $row['order_time'];
    break;
};

$timeString = (string)"+$orderTime day";
$time2 = strtotime($timeString); // Unix формат

$time = date('Y-m-d', $time2);
$time = "'$time'";

$query = "SELECT * FROM clients_comments WHERE status = (SELECT id FROM claim_status AS s WHERE s.key = 'check_processing')";

$resultsComment = mysqli_query($link, $query);

$comments = array();

while($row = mysqli_fetch_assoc($resultsComment)){
    $comments[] = $row;
}

foreach($comments as $comment){
    $date = '0000-00-00';
    $commentId = $comment['id'];
    $query = "SELECT * FROM product WHERE id IN ((SELECT product FROM client_comment_join_product WHERE comment = $commentId))";

    $resultsProduct = mysqli_query($link, $query);

    $products = array();

    while($row = mysqli_fetch_assoc($resultsProduct)){
        $products[] = $row;
    }


    foreach($products as $product){
        if($product['delivery_time'] > $date){
            $date = $product['delivery_time'];
        }
    }

    if($date < $orderTime){
        $queryClient = "SELECT client FROM clients_comments WHERE id = $commentId";

        $resultsClient = mysqli_query($link, $queryClient);
        $client = mysqli_fetch_assoc($resultsClient);
        echo json_encode(['status' => 'remember', 'data' => $comment, 'client' => $client['client'] ]); die;
    }

}

echo json_encode(['status' => 'nothing']);