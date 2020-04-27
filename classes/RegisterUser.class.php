<?php


class RegisterUser


{

    public $id;
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

        try{
            $sth = $this->dbase->prepare("insert into Users verCode value :id ;");
            $sth->bindParam(':id', $userData['verCode']);
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
        $stmt = $this->db->prepare("SELECT email, username, firstname, lastname, date, verified, admin FROM Users WHERE username=:username");
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

    public function editUser(User $user): bool {
        $firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
        $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $verified = filter_input(INPUT_POST, 'verified', FILTER_SANITIZE_NUMBER_INT);
        $hash = password_hash($password, PASSWORD_DEFAULT);

        if ($verified == null) $verified = 1;

        try {
            $sth = $this->db->prepare("update Users set firstname = :firstname, lastname = :lastname, password = :hash where username = :username");
            $sth->bindParam(':firstname', $firstname);
            $sth->bindParam(':lastname', $lastname);
            $sth->bindParam(':hash', $hash);

            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->NotifyUser('User details changed', '');
            } else {
                $this->NotifyUser('Failed to change user details', "");
            }
        } catch (Exception $e) {
            $this->NotifyUser('Error 23', $e->getMessage() . PHP_EOL);
        }
    }

    public function deleteUser($username): bool {
        $result = false;
        try
        {
            $stmt = $this->db->prepare("DELETE FROM Users WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount()==1) {
                $this->NotifyUser( "User deleted", "");
                $result = true;
            } else {
                $this->NotifyUser( "Error 3", "");
                $result = false;
            }
        }
        catch (Exception $e) {
            $this->NotifyUser( "En feil oppstod", $e->getMessage() . PHP_EOL);
        }
        return $result;
    }

    public function verifyUser($request) : bool {

        if($request->query->get('id')){
            try{
                $sql= query("select * from Users where 'verCode'= 'id'");
                query($sql);
                if($sql != null ){
                    return true;
                }
                else
                    return false;
            }catch (Exception $e){
                $this->NotifyUser("En feil oppstod", $e->getMessage() . PHP_EOL);
            }
        }
    }

}

?>