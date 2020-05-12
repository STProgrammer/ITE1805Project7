<?php

require_once('../includes.php');

$regUser = new RegisterUser($db, $request, $session);

if ($request->request->get('verify') == 'verify' and XsrfProtection::verifyMac("Send-email")) {
    $email = $request->request->get('email');
    $regUser->sendEmail($email);
    header("Location: ./verify.php?emailsent=1");
    exit();
}

$verified = $regUser->verifyUser();

echo $twig->render('verifyEmail.twig', array('verified'=>$verified, 'rel' => $rel, 'session' => $session));

?>
