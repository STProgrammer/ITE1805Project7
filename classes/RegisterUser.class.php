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

    public function editUser(User $user) {
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

    //source code: https://phpgurukul.com/change-password-php/
    //https://stackoverflow.com/questions/34320881/php-pdo-how-do-i-change-password-of-logged-in-user-via-php-and-pdo-and-refresh?fbclid=IwAR1IeTq9403lCm9SFjwtzUYuq-_8sN7EoLGPaWlYhRYEV7FJFg8LuXXTrag

    public function changePassword($username)
    {
        $password = null;
        $old_password = filter_input(INPUT_POST, 'oldpassword', FILTER_SANITIZE_STRING);        
        $oldhash = password_hash($old_password, PASSWORD_DEFAULT);
        $new_password = filter_input(INPUT_POST, 'newpassword', FILTER_SANITIZE_STRING);
        $cf_password = filter_input(INPUT_POST, 'cfpassword', FILTER_SANITIZE_STRING);
        $newhash = password_hash($new_password, PASSWORD_DEFAULT);

        if($new_password !== $cf_password){
            $this->NotifyUser('', 'New Password and confirmation does not match');
            return false;
        }
        try {
            $sth = $this->dbase->prepare("SELECT password FROM Users WHERE password =:oldhash && username = :username");
            $sth->bindParam(':oldhash', $oldhash);
            $sth->bindParam(':username', $username);
            $sth->execute();
            if ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $sth2 = $this->dbase->prepare("UPDATE Users set password =:newhash WHERE username = :username");
                $sth2->bindParam(':newhash', $newhash);
                $sth2->bindParam(':username', $username);
                $sth2->execute();
                $password = $sth2->fetchAll();
                $this->NotifyUser('', 'Password sucessfull changed');
                return $password;
            } else {
                $this->NotifyUser('', 'Old Password does not match');
                return false;
            }
        } catch (Exception $e){
            $this->NotifyUser('Error 23', $e->getMessage() . PHP_EOL);
            return false;
        }
    }


}

?>