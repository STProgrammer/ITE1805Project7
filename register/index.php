<?php

    spl_autoload_register(function ($class_name) {
        require_once "../classes/" . $class_name . '.class.php';
    });
    session_start();
    require_once '../../vendor/autoload.php';

    $loader = new \Twig\Loader\FilesystemLoader('../templates');
    $twig = new \Twig\Environment($loader);

    $db = Db::getDBConnection();

    $reguser = new RegisterUser($db);


    if (isset($_POST['register'])) {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST,'password', FILTER_SANITIZE_EMAIL);
        $reguser->registerUser($username, $name, $lastname, $email, $password);

    } else {
        echo $twig->render('register.twig', array());
    }



?>