<?php

require_once('../../includes.php');

$regUser = new RegisterUser($db, $request, $session);

$verified = $regUser->verifyUser();

echo $twig->render('verify-email.twig', array('verified'=>$verified, 'rel' => $rel));

?>
