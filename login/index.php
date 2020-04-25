<?php

require_once '../includes.php';

if ($session->has('loggedin')) {
    $user = $session->get('User'); // get the user data
    header("Location: ..");
    exit();
}

// if login submitted
elseif ($request->request->has('login')) {
    if(XsrfProtection::verifyMac("Login") && User::login($db, $request, $session)) {
        $user = $session->get('User');
        if ($session->get('loggedin') && $user->verifyUser($request)) {
            header("Location: ..");
            exit();
        }
    } //if login submitted but failed to login
    else {
        $get_info = "?loginfail=1";
        header("Location: ../login".$get_info);
        exit();
    }
}

if ($request->query->has('loginfail')) {
    echo $twig->render('login.twig', array('fail' => true));
} else {
    echo $twig->render('login.twig', array());
}

?>