<?php

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

$queryParams = "SELECT claim_time FROM setup WHERE id =1";

$results = mysqli_query($link, $queryParams);

while ($row = mysqli_fetch_assoc($results)) {
    $claimTime = $row['claim_time'];
    break;
};

$timeString = (string)"-$claimTime min";
$time2 = strtotime($timeString); // Unix формат

$time = date('Y-m-d H:i:s', $time2);
$time = "'$time'";

$queryComment = "SELECT * FROM clients_comments WHERE createdAt < $time
                  AND id NOT IN ((SELECT comment FROM client_comment_join_product))";


$results = mysqli_query($link, $queryComment);
$comments = array();

while ($row = mysqli_fetch_assoc($results)) {
    $comments[] = $row;
};

if(!empty($comments)){
    echo json_encode(['status' => 'remember', 'data' => $comments[0] ]);
}else {
    echo json_encode(['status' => 'nothing']);
}