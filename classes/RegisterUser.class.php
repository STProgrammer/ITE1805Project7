<?php


class RegisterUser
{

    public function __construct(PDO $db)
    {
        $this->dbase = $db;
    }

    //Register user
    public function registerUser ($username, $name, $lastname, $email, $password)
    {
        try{
            $hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);


            $sth = $this->dbase->prepare("insert into Users (email, password, username, name, lastname, date) values (:email, :hash, :username, :name, :lastname, NOW());");
            $sth->bindParam(':email', $email);
            $sth->bindParam(':hash', $hash);
            $sth->bindParam(':username', $username);
            $sth->bindParam(':name', $name);
            $sth->bindParam(':lastname', $lastname);
            $sth->execute() or exit();
        } catch (Exception $e){
            print $e->getMessage() . PHP_EOL;
        }
    }

}