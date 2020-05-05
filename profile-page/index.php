<?php

require_once '../includes.php';

//Håndterer login
require_once "../login.php";

//First we use reguser

$regUser = new RegisterUser($db, $request, $session);

//User information
//We get the username from $_GET and get the user data to use
if(($username = $request->query->get('username')) && $userData = $regUser->getUserData($username)) {
    //   $userData = $regUser->getUserData($username);
    $isOwner = false;  //isOwner controls if the user owns this account or not, this is to avoid repeated checks
    $isAdmin = false;  //isAdmin controls if the user is admin or not, this is to avoid repeated checks
    //we check if the user of the website is logged in, and verified
    if (($user = $session->get('User')) && $session->get('loggedin') && $user->verifyUser($request)) {
        if ($user->isAdmin() == 1) {
            $isAdmin = true;
        }  //check if user is Admin
        if ($user->getUserName() == $userData['username']) {
            $isOwner = true;
        }
    } // End checking owner and admin


    echo $twig->render('profile-page.twig', array('user' => $user, 'request' => $request, 'session' => $session,
        'rel' => $rel, 'isOwner' => $isOwner, 'userData' => $userData));
}

else {
    header("Location: .." );
    exit();
}

?>