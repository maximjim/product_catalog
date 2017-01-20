<?php
session_start();
define('PASSWORD', 'admin123');
define('LOGIN', 'admin');

if(!isset($_SESSION['user'])){
    if(!empty($_POST) && isset($_POST['login']) && isset($_POST['password'])){
        $login = $_POST['login'];
        $password = $_POST['password'];

        if($login === LOGIN && $password === PASSWORD){
            $_SESSION['user'] = array('login' => $login, 'password' => $password);
            header('location: /');
            die;
        }else {
            $error = 'Неверная комбинация логина и пароля';
        }
    }

    include 'login.phtml';
} else{
    header('location: /');
}