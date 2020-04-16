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

    $userData = array();


    if (isset($_POST['register'])) {
        $userData['username'] = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $userData['firstname'] = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
        $userData['lastname'] = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
        $userData['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $userData['password'] = filter_input(INPUT_POST,'password', FILTER_SANITIZE_EMAIL);
        $reguser->registerUser($userData);

    } else {
        echo $twig->render('register.twig', array());
    }



?>