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
            $hash = password_hash($userData['password'], PASSWORD_DEFAULT);
            $sth = $this->dbase->prepare("insert into Users (email, password, username, firstname, lastname, date, verified) values (:email, :hash, :username, :firstname, :lastname, NOW(), 1);");
            $sth->bindParam(':email', $userData['email']);
            $sth->bindParam(':hash', $hash);
            $sth->bindParam(':username', $userData['username'] );
            $sth->bindParam(':firstname', $userData['firstname']);
            $sth->bindParam(':lastname',  $userData['lastname']);
            $sth->execute() or exit();
       //     $this->sendEmail($userData);
        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    private function sendEmail($userData) {
        $message = "This is a test message";
        ini_set("SMTP", "localhost");
        ini_set("smtp_port", "25");
        ini_set("sendmail_from", "abd.karagoz@gmail.com");
        mail( $userData['email'], "your feedback", $message, "From: website");
    }

}