<!DOCTYPE html>
<html lang="no">
<head>
    <link rel="stylesheet" type="text/css" href="stil.CSS" />
    <title>Student Register</title>
</head>
<body>
<h1>Student register</h1>

<?php
require_once "../../includes.php";

$session->getFlashBag()->add('notice', "Test message");

echo  $twig->render('thiss.twig', array('session' => $session));


?>