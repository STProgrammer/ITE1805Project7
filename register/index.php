<?php

    require_once('../includes.php');
    require_once ('../login.php');
    include('process.php');

    $reguser = new RegisterUser($db, $request, $session);

    $userData = array();

    if ($request->request->has('register') && XsrfProtection::verifyMac("Register")) {
        $reguser->registerUser();
        header("Location: ../?registereduser=1");
        exit();

    } else {
        echo $twig->render('register.twig', array('script' => $homedir, 'rel' => $rel, 'user' => $user));
    }
?>