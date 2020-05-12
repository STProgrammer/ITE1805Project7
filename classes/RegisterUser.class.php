<?php


class RegisterUser
{
    private $dbase;
    private $request;
    private $session;

    public function __construct(PDO $db, \Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\HttpFoundation\Session\Session $session)
    {
        $this->dbase = $db;
        $this->request = $request;
        $this->session = $session;
    }



    private function notifyUser($strHeader, $strMessage)
    {
        $this->session->getFlashBag()->add('header', $strHeader);
        $this->session->getFlashBag()->add('message', $strMessage);
    }


    //Register user
    public function registerUser ()
    {
        $username = $this->request->request->get('username');
        $firstname = $this->request->request->get('firstname');
        $lastname = $this->request->request->get('lastname');
        $email = $this->request->request->get('email');
        $password = $this->request->request->get('password');
        try{
            //check if username exists
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sth = $this->dbase->prepare("insert into Users (email, password, username, firstname, lastname, date, verified) values (:email, :hash, :username, :firstname, :lastname, NOW(), 0);");
            $sth->bindParam(':email', $email, PDO::PARAM_STR);
            $sth->bindParam(':hash', $hash, PDO::PARAM_STR);
            $sth->bindParam(':username', $username, PDO::PARAM_STR);
            $sth->bindParam(':firstname', $firstname, PDO::PARAM_STR);
            $sth->bindParam(':lastname',  $lastname, PDO::PARAM_STR);
            $sth->execute();
            if ($this->sendEmail($email)) { $this->notifyUser("User registered", "");}
            else {$this->notifyUser("Failed to send email to verify!", ""); }
        } catch (Exception $e) {
            $this->notifyUser("Failed to register user!","");
        }
    }



    public function sendEmail(string $email) : bool {
        $ch = curl_init();
        //Koden for å hente URL adresse er tatt og modifisert fra https://www.javatpoint.com/how-to-get-current-page-url-in-php
        if($this->request->server->get('HTTPS') === 'on')
            $url = "https://";
        else
            $url = "http://";
        // Append the host(domain name, ip) to the URL.
        $url .= $this->request->server->get('HTTP_HOST');
        // Append the requested resource location to the URL
        $url .= dirname($this->request->server->get('PHP_SELF'));
        $url .= "/verify.php";
        $timestamp = time();

        $id = md5(uniqid(rand(), 1));
        try{
            $sth = $this->dbase->prepare("update Users set verCode = :id, verified = 0, timestamp = :timestamp where email = :email;");
            $sth->bindParam(':email', $email, PDO::PARAM_STR);
            $sth->bindParam(':id',  $id, PDO::PARAM_STR);
            $sth->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser("Email sent, check your inbox for verification","");
            } else {
                $this->notifyUser("Failed to send verification email, email not found","");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to send verification email","");
            return false;
        }
        curl_setopt($ch, CURLOPT_URL, "https://kark.uit.no/internett/php/mailer/mailer.php?address=".$email."&url=".$url ."?id=". $id);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return true;
    } //END send Email



    public function verifyUser() : bool {
        $timeLimit = 86400;  //86,400 seconds is one day

        if($id = $this->request->query->get('id')) {
            try {
                $sth = $this->dbase->prepare("select timestamp from Users where vercode = :id");
                $sth->bindParam(':id', $id, PDO::PARAM_STR);
                $sth->execute();
                $timestamp = $sth->fetchColumn();
                if (time() - $timestamp > $timeLimit) {
                    $this->notifyUser("Verification code has expired", "");
                    return false;
                } else {
                    $sth = $this->dbase->prepare("update Users set verified = 1 where verCode = :id");
                    $sth->bindParam(':id', $id, PDO::PARAM_STR);
                    $sth->execute();
                    if($sth->rowCount() == 1) {
                        $this->notifyUser("Email verified", "");
                        return true;
                    }
                    else {
                        $this->notifyUser("Failed to verify email", "");
                        return false;
                    }
                }


            } catch (Exception $e) {
                $this->notifyUser("Failed to verify email", "");
                return false;
            }
        } return false;
    }


    public function getUserData(string $username) {
        try {
            $stmt = $this->dbase->prepare("SELECT email, username, firstname, lastname, date, verified, admin FROM Users WHERE username=:username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $row;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to get user data", "");
        }
    }

    /*
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
                $this->notifyUser("User not found", "");
                return new User();
            }
        }
        catch(Exception $e) { $this->notifyUser("Something went wrong!", "");
            return new User();}
    }

    public function getAllUsers(string $username){
        $allUsers = null;
        try{
            $stmt = $this->dbase->prepare("SELECT email, username, firstname, lastname, date, verified, admin FROM Users WHERE username=:username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $allUsers = $stmt->fetchAll();
        }  catch (Exception $e) { $this->notifyUser("Something went wrong!", ""); return; }

        return $allUsers;
    }*/

    public function editUser(string $username) : bool {
        $newUsername = $this->request->request->get('username');
        $firstname = $this->request->request->get('firstname');
        $lastname = $this->request->request->get('lastname');
        $verified = $this->request->request->get('verified');

        if (!$this->isUsernameAvailable($username, $newUsername)) {
            $this->notifyUser("Failed to edit, username was already taken", "");
            return false;
        }

        if ($verified == null) $verified = 1;

        try {
            $sth = $this->dbase->prepare("update Users set firstname = :firstname, lastname = :lastname, username = :newUsername where username = :username");
            $sth->bindParam(':newUsername', $newUsername, PDO::PARAM_STR);
            $sth->bindParam(':username', $username, PDO::PARAM_STR);
            $sth->bindParam(':firstname', $firstname, PDO::PARAM_STR);
            $sth->bindParam(':lastname', $lastname, PDO::PARAM_STR);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->session->get('User')->setFirstName($firstname);
                $this->session->get('User')->setLastName($lastname);
                $this->session->get('User')->setUsername($newUsername);
                $this->notifyUser('User details changed', '');
                return true;
            } else {
                $this->notifyUser('Failed to change user details', "");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to change user details", "");
            return false;
        }
    }

    private function isUsernameAvailable(string $username, string $newUsername) : bool {
        if ($username == $newUsername) {
            return true;
        }
        else {
            try {
                $stmt = $this->dbase->prepare("SELECT count(*) as cntUser FROM Users WHERE username = :newUsername");
                $stmt->bindParam(':newUsername', $newUsername, PDO::PARAM_STR);
                $stmt->execute();
                $count = $stmt->fetchColumn();
                if($count > 0){
                    return false;
                } else {
                    return true;
                }
            } catch (Exception $e) {
                $this->notifyUser("Something went wrong", "");
                return false;
            }
        }
    }


    public function changePassword(string $password, string $username) : bool {
        if ($password == "") {return false;}
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $sth = $this->dbase->prepare("update Users set password = :hash where username = :username");
            $sth->bindParam(':username', $username, PDO::PARAM_STR);
            $sth->bindParam(':hash', $hash, PDO::PARAM_STR);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser("Password changed!", '');
                return true;
            } else {
                $this->notifyUser('Failed to change password!', "");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to change password", "");
            return false;
        }
    }


    public function changeEmail(string $email, string $username) : bool {
        if (!$this->isEmailAvailable($email)) {
            $this->notifyUser("Failed to change because email was already taken", '');
            return false;
        }
        try {
            $sth = $this->dbase->prepare("update Users set email = :email, verified = 0 where username = :username");
            $sth->bindParam(':username', $username, PDO::PARAM_STR);
            $sth->bindParam(':email', $email, PDO::PARAM_STR);
            $sth->execute();
            $this->sendEmail($email);
            if ($sth->rowCount() == 1) {
                $this->notifyUser("Email changed", '');
                return true;
            } else {
                $this->notifyUser("Email not changed!", "");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to change email!", "");
            return false;
        }
    }



    private function isEmailAvailable(string $newEmail) : bool {
        $email = $this->session->get('User')->getEmail();
        if ($email == $newEmail) {
            return true;
        }
        else {
            try {
                $stmt = $this->dbase->prepare("SELECT count(*) as cntUser FROM Users WHERE email = :newEmail");
                $stmt->bindParam(':newEmail', $newEmail, PDO::PARAM_STR);
                $stmt->execute();
                $count = $stmt->fetchColumn();
                if($count > 0){
                    return false;
                } else {
                    return true;
                }
            } catch (Exception $e) {
                $this->notifyUser("Something went wrong", "");
            }
        }
    }


    public function deleteUser(string $username) {
        try
        {
            $stmt = $this->dbase->prepare("DELETE FROM Users WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount()==1) {
                $this->notifyUser( "User deleted", "");
            } else {
                $this->notifyUser( "Failed to delete user!", "");
            }
        }
        catch (Exception $e) {
            $this->notifyUser( "Failed to delete user!", "");
        }
    }
}

?>