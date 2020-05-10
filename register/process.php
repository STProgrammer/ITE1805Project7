<?php

require_once '../includes.php';

//Denne koden er tatt og litt modifisert fra https://codewithawa.com/posts/check-if-user-already-exists-without-submitting-form

if ($request->request->has('username_check')) {
    try {
        $username = $request->request->get('username');
        $stmt = $db->prepare("SELECT count(*) as cntUser FROM Users WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        if($count > 0){
            echo "taken";
        } else {
            echo 'not_taken';
        }
    } catch (Exception $e) {
        $this->notifyUser("Something went wrong", $e->getMessage() . PHP_EOL);
    }
    exit();
}
if ($request->request->has('email_check')) {
    try {
        $email = $request->request->get('email');
        $stmt = $db->prepare("SELECT count(*) as cntUser FROM Users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        if($count > 0) {
            echo "taken";
        } else {
            echo 'not_taken';
        }
    } catch (Exception $e) {
        $this->notifyUser("Something went wrong", $e->getMessage() . PHP_EOL);
    }
    exit();
}


?>