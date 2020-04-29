<?php

require_once('../includes.php');

$regUser = new RegisterUser($db, $request, $session);

$verified = $regUser->verifyUser();

echo $twig->render('verifyEmail.twig', array('verified'=>$verified));

?>