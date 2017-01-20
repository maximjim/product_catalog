<?php

include "../security/checkLogin.php";

// Получаем подключение к БД из файла подключение
$link = include '../params/connectDB.php';

$queryIncome = "SELECT SUM(amount) AS sum FROM clients_comments";

$resultIncome = mysqli_query($link, $queryIncome);

$resultIncomeValue = 0;

while ($row = mysqli_fetch_assoc($resultIncome)) {
    $resultIncomeValue = $row;
    break;
};


$queryExpense = "SELECT SUM(amount) AS sum FROM consignments";

$resultExpense = mysqli_query($link, $queryExpense);

$resultExpenseValue = 0;

while ($row = mysqli_fetch_assoc($resultExpense)) {
    $resultExpenseValue = $row;
    break;
};

$result = ($resultIncomeValue['sum'] - $resultExpenseValue['sum']);

return $result;