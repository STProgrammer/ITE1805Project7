<?php


class RegisterUser


{

    public function __construct(PDO $db, \Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\HttpFoundation\Session\Session $session)
    {
        $this->dbase = $db;
        $this->request = $request;
        $this->session = $session;

    }

    private function NotifyUser($strHeader, $strMessage)
    {
        $this->session->getFlashBag()->add('header', $strHeader);
        $this->session->getFlashBag()->add('message', $strMessage);
    }

    //Register user
    public function registerUser ($userData)
    {
        $username = $this->request->request->get('username');
        $firstname = $this->request->request->get('firstname');
        $lastname = $this->request->request->get('lastname');
        $email = $this->request->request->get('email');
        $password = $this->request->request->get('password');

        try{
            $hash = password_hash($userData['password'], PASSWORD_DEFAULT);
            $sth = $this->dbase->prepare("insert into Users (email, password, username, firstname, lastname, date, verified) values (:email, :hash, :username, :firstname, :lastname, NOW(), 0);");
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

        try{
            $sth = $this->dbase->prepare("update Users set verCode = :id where email = :email;");
            $sth->bindParam(':email', $email);
            $sth->bindParam(':id', $id);
            $sth->execute();
        }catch (Exception $e){
            print $e->getMessage() . PHP_EOL;
        }


        curl_setopt($ch, CURLOPT_URL, "https://kark.uit.no/internett/php/mailer/mailer.php?address=".$email."&url=".$url ."?id=". $id);

        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);

        echo $output;

        curl_close($ch);

    }

    public function getUserData($username){
        $stmt = $this->dbase->prepare("SELECT email, username, firstname, lastname, date, verified, admin FROM Users WHERE username=:username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR, strlen($username));
        $stmt->execute();
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row;
        }
    }

    public function getUserObject ($username) : User {
        try
        {
            $stmt = $this->db->prepare("SELECT email, password, username, firstname, lastname, date, verified, admin FROM Users WHERE username=:username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            if($usr = $stmt->fetchObject('User')) {
                return $usr;
            }
            else {
                $this->NotifyUser("User not found", "");
                return new User();
            }
        }
        catch(Exception $e) { $this->NotifyUser("Error 7", $e->getMessage());
            return new User();}

    }

    public function getAllUsers(string $username){
        $allUsers = null;
        try{
            $stmt = $this->db->prepare("SELECT email, username, firstname, lastname, date, verified, admin FROM Users WHERE username=:username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $allUsers = $stmt->fetchAll();
        }  catch (Exception $e) { $this->NotifyUser("En feil oppstod", $e->getMessage()); return; }

        return $allUsers;
    }

    public function editUser($username) {
        $newUsername = $this->request->request->get('username');
        $firstname = $this->request->request->get('firstname');
        $lastname = $this->request->request->get('lastname');
        $verified = $this->request->request->get('verified');

        if ($verified == null) $verified = 1;

        try {
            $sth = $this->dbase->prepare("update Users set firstname = :firstname, lastname = :lastname, username = :newUsername where username = :username");
            $sth->bindParam(':newUsername', $newUsername);
            $sth->bindParam(':username', $username);
            $sth->bindParam(':firstname', $firstname);
            $sth->bindParam(':lastname', $lastname);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->session->get('User')->setFirstName($firstname);
                $this->session->get('User')->setLastName($lastname);
                $this->session->get('User')->setUsername($newUsername);
                $this->notifyUser('User details changed', '');
            } else {
                $this->notifyUser('Failed to change user details', "");
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to change user details", $e->getMessage() . PHP_EOL);
        }
    }

    public function changePassword($password, $username) : bool {
        if ($password == "") {return false;}
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $sth = $this->dbase->prepare("update Users set password = :hash where username = :username");
            $sth->bindParam(':username', $username);
            $sth->bindParam(':hash', $hash);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser("Password changed!", '');
                return true;
            } else {
                $this->notifyUser('Failed to change password!', "");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to change password", $e->getMessage() . PHP_EOL);
            return false;
        }
    }


    public function changeEmail($email, $username) {
        try {
            $sth = $this->dbase->prepare("update Users set email = :email, verified = 0 where username = :username");
            $sth->bindParam(':username', $username);
            $sth->bindParam(':email', $email);
            $sth->execute();
            $this->sendEmail($email);
            if ($sth->rowCount() == 1) {
                $this->notifyUser("Email changed", '');
                return true;
            } else {
                $this->notifyUser("Failed to change email!", "");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to change email!", $e->getMessage() . PHP_EOL);
            return false;
        }
    }

    public function deleteUser($username) {
        try
        {
            $stmt = $this->dbase->prepare("DELETE FROM Users WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount()==1) {
                $this->notifyUser( "User deleted", "");
                $result = true;
            } else {
                $this->notifyUser( "Failed to delete user!", "");
                $result = false;
            }
        }
        catch (Exception $e) {
            $this->notifyUser( "Failed to delete user!", $e->getMessage() . PHP_EOL);
        }
    }

    public function verifyUser() : bool {

        if($this->request->query->get('id')){
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
            try{
                $sth  = $this->dbase->prepare("update Users set verified = 1 where verCode = :id");
                $sth ->bindParam(':id',$id);
                $sth ->execute();
                if($sth->rowCount() == 1 ){
                    return true;
                }
                else
                    return false;
            }catch (Exception $e){
                $this->NotifyUser("En feil oppstod", $e->getMessage() . PHP_EOL);
                return false;
            }
        }
        else
            return false;
    }

}

?>