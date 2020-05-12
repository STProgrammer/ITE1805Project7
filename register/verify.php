<?php

require_once('../includes.php');



$regUser = new RegisterUser($db, $request, $session);

if ($request->query->has('id')) {
    $verified = $regUser->verifyUser();
    $session->clear();
    echo $twig->render('verify-email.twig', array('verified' => $verified, 'rel' => $rel, 'session' => $session));
}

//Logout
elseif ($request->request->has('logout') && XsrfProtection::verifyMac("Logout")) {
    $session->clear();
    header('location: ../');
    exit();
}

//Email sent for verification
elseif ($request->request->has('verify')&& XsrfProtection::verifyMac("send-email")) {
    $email = $request->request->get('email');
    $regUser->changeEmail($email, $session->get('User')->getUsername());
    $session->clear();
    header("Location: ../?emailsent=1");
    exit();
}

//User loggedin but not verified
elseif ($session->has('loggedin') && ($user = $session->get('User')) && ($user->isVerified() == 0)) {
    echo $twig->render('verify-email.twig', array('rel' => $rel, 'user' => $user, 'session' => $session));
}

else {
    header("Location: ../");
    exit();
}

?>
