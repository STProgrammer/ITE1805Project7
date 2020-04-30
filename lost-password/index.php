<!DOCTYPE html>
<html>
<head>
    <title>ITE1805Project7</title>
    <meta charset="utf-8" />
    <style>
        input {display:block;}
    </style>
</head>
<body>
<h1>ITE1805Project7</h1>
<h2>Reset Password</h2>

<?php

require_once '../includes.php';

$validation=false;
$username = "";
$email = "";

if(isset($_POST['submit']) &&  !empty($_POST['submit'])) {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $stmt = $db->prepare("SELECT email, username FROM Users WHERE (username = :username) and (email = :email)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if($stmt->rowCount() >= 1 ){
        echo $twig->render('change-password.twig', array()); //change-password.twig er bare en midlertidig hoppside for å teste om koden er korrekt. Den blir skrevet om senere.
        $validation = true;
    }
    else
        echo 'You must enter your username and E-mail address correctly to reset your password.';
}

if(!$validation){

    ?>
    <form method="post">

    <table border="1" cellspacing="0" cellpadding="5" width="400">
        <tr>
        <tr height="50"><td><label>Username: </label>
            <td><input type="text" name="username"></td></tr>
        <tr height="50"><td><label>E-mail address: </label>
            <td><input type="email" name="email"></td></tr>

        <td><input type="submit" name="submit" value="Reset password" /></td>
        </tr>
    </table>
    <?php

}

?>

