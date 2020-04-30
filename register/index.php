<?php

    require_once('../includes.php');

    $reguser = new RegisterUser($db, $request, $session);

    $userData = array();


    if ($request->request->has('register') && XsrfProtection::verifyMac("Register")) {
        $reguser->registerUser($userData);

    } else {
        echo $twig->render('register.twig', array('script' => $homedir));
    }



?>