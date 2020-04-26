<?php


class RegisterUser
{

    public function __construct(PDO $db, \Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\HttpFoundation\Session\Session $session)
    {
        $this->dbase = $db;
        $this->request = $request;
        $this->session = $session;
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
            $this->sendEmail($userData);
        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    private function sendEmail($userData) {

        $ch = curl_init();
        $email = $userData['email'];
        //Koden for å hente URL adresse er tatt og modifisert fra https://www.javatpoint.com/how-to-get-current-page-url-in-php
        if($this->request->server->get('HTTPS') === 'on')
            $url = "https://";
        else
            $url = "http://";
        // Append the host(domain name, ip) to the URL.
        $url .= $this->request->server->get('HTTP_HOST');
        // Append the requested resource location to the URL
        $url .= dirname($this->request->server->get('PHP_SELF'));
        $url .= "/verify.php/";

        $id = md5(uniqid(rand(), 1));
        curl_setopt($ch, CURLOPT_URL, "https://kark.uit.no/internett/php/mailer/mailer.php?address=".$email."&url=".$url ."?=". $id);

        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);

        echo $output;

        curl_close($ch);
    }

}