<?php

    require_once('../includes.php');

    $reguser = new RegisterUser($db);

    $userData = array();


    if ($request->request->has('register')) {
        $userData['username'] = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $userData['firstname'] = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
        $userData['lastname'] = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
        $userData['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $userData['password'] = filter_input(INPUT_POST,'password', FILTER_SANITIZE_EMAIL);
        $reguser->registerUser($userData);

    } else {
        echo $twig->render('register.twig', array('script' => $homedir));
    }



?>