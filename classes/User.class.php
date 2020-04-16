<?php
class User {

    private $db;                // reference to PDO connection object
    private $id;
    private $loggedin = false;
    private $sessionUser=null;

    public function __construct(PDO $db) {

        $this->db = $db;

        if (isset($_POST['login'])) {
            //sjekk om brukernavn og passord er riktig
            $this->login($_POST['username'], $_POST['password']);
        } else if (isset($_POST['logout'])) {
            unset($_SESSION['loggedin']);
            unset($_SESSION['uid']);
            unset($_SESSION['navn']);
            unset($_SESSION['bruker']);
            $this->innlogget = false;
        } else if (isset($_SESSION['loggedin'])) {
            $this->loggedin = true;
            $this->sessionUser = $_SESSION['bruker'];
        }
    }

    public function loggedIn() : bool {
        return $this->loggedin;
    }

    public function login( $username, $password) : array {

        $stmt = $this->db->prepare("SELECT userId,  email, password, username, firstname, lastname, date, verified, admin FROM Users WHERE username=:username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC) )
        {
            if (password_verify($password, $row['password']) && $row['verified'] == 1) {
                $_SESSION['loggedin'] = true;
                $this->loggedin = true;
                $this->id = $row["userId"];
                $this->sessionUser= $_SESSION['bruker'] = new SessionUser($username , $row["firstname"]  .  " " . $row["lastname"] );
                return array('status'=>'OK');
            }else return array('status'=>'FAIL', 'errorMessage'=>'Bad password');

        }
        else return array('status'=>'FAIL', 'errorMessage'=>'Bad password');
    }
    public function verifyUser() : bool {

        return $this->sessionUser->verifyUser();
    }
    public function getHits() : int {

        return $this->sessionUser->getHits();
    }
    public function getId() : int {

        return $this->id;
    }

    public function getFullName() : string {

        return $this->sessionUser->getFullName();
    }
}

?>