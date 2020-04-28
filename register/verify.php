<?php

require_once('../includes.php');

$reguser = new RegisterUser($db, $request, $session);

$userData = array();

$id = $request->query->get('id');

$status = $request->query->get('verified');

$username = $request->query->get('username');

$verCode  = $reguser->dbase->query("SELECT verCode from Users where username = ':username'");

$stmt = $db->prepare("SELECT verCode from Users where username = ':username'");
$stmt->bindParam(':username', $username);
$stmt->execute();

echo $twig->render('verifyEmail.twig', array('verCode'=>$verCode,'id'=>$id));