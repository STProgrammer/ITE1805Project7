<?php

class User {

    private $usr_name;          // Holds the users username
    private $usr_full_name;     // Holds the users full name
    private $IPAddress;         // Holds the users login IP address
    private $UserAgent;         // Holds the users user agent (browser ID)
    private $usr_hits;         // Holds the users hitcount
    private $admin;
    private $verified;
    private $date;
    private $email;

    function __construct(string $email, string $fn, string $ip, string $browser, array $row ) {
        $this->email = $email;
        $this->usr_name = $row['username'];
        $this->usr_full_name = $fn;
        $this->IPAddress = $ip;
        $this->UserAgent = $browser;
        $this->usr_hits = 0;
        $this->admin = $row['admin'];
        $this->verified = $row['verified'];
        $this->date = $row['date'];


    }

    public function setName($newname) { $this->usr_full_name = $newname; }
    public function getUsername() { return $this->usr_name; }
    public function getEmail() { return $this->email;}
    public function getFullName() { return $this->usr_full_name; }
    public function isAdmin() { return $this->admin; }
    public function makeAdmin() { $this->admin = 1; }
    public function undoAdmin() { $this->admin = 0; }
    public function isVerified() { return $this->verified; }
    public function setVerified() { $this->verified = 1; }
    public function getHits() { return $this->usr_hits; }
    public function getIPAddress() { return $this->IPAddress; }
    public function verifyUser($request) {
        //$request = Request::createFromGlobals();
        if(($this->IPAddress == $request->server->get('REMOTE_ADDR')) && ($this->UserAgent == $request->server->get('HTTP_USER_AGENT') )){
            $this->usr_hits++;
            return true;
        }
        else
            return false;
    }


    public static function login(PDO $db,  $request,  $session) {
        $email = $request->request->get('email');
        $stmt = $db->prepare("SELECT username, password, username, firstname, lastname, date, verified, admin FROM Users WHERE email=:email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            if (password_verify($request->request->get('password'), $row['password']) && $row['verified'] == 1) {
                $lastname = $row["lastname"];
                $firstname = $row["firstname"];
                $session->set('loggedin', true);
                $ip = $request->server->get('REMOTE_ADDR');
                $browser = $request->server->get('HTTP_USER_AGENT');
                $session->set('User', new User($request->request->get('email'), $firstname . " " . $lastname, $ip, $browser, $row));
                return true;
            }
        }
        else return false;
    }
}
?>