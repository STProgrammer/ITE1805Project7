<?php


class RegisterUser
{

    public function __construct(PDO $db)
    {
        $this->dbase = $db;
    }

    //Register user
    public function registerUser ($userData)
    {
        try{
            $hash = password_hash($userData['password'], PASSWORD_DEFAULT, ['cost' => 12]);
            $sth = $this->dbase->prepare("insert into Users (email, password, username, name, lastname, date) values (:email, :hash, :username, :name, :lastname, NOW());");
            $sth->bindParam(':email', $userData['email']);
            $sth->bindParam(':hash', $hash);
            $sth->bindParam(':username', $userData['username'] );
            $sth->bindParam(':name', $userData['name']);
            $sth->bindParam(':lastname',  $userData['lastname']);
            $sth->execute() or exit();
            $this->sendEmail($userData);
        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    private function sendEmail($userData) {
        $message = "This is a test message";
        mail( $userData['email'], "your feedback", $message, "From: website");
    }

}