<?php
// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

$status = $_POST['value'];
$comment = $_POST['pk'];

$query = "UPDATE clients_comments SET status = $status WHERE id = $comment";
mysqli_query($link, $query);


echo json_encode(true);