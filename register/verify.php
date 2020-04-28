<?php

require_once('../includes.php');

$reguser = new RegisterUser($db, $request, $session);

$userData = array();

$status = $request->query->get('verified');

if( $status == 1 ){
    echo $twig->render('verifyEmail.twig',array());
}
else{
    echo $twig->render('verifyEmail.twig',array());
}