<?php

class XsrfProtection
{
    public function __construct()
    {
    }

    public static function getMac($action_name) {
        $key = "sha1";
        $secret = "g45jf722e";
        $id = session_id();
        return hash_hmac($key, $action_name . $secret, $id);
    }

    public static function verifyMac($action_name) : bool {
        $key = "sha1";
        $secret = "g45jf722e";
        $id = session_id();
        $new_mac = hash_hmac($key, $action_name . $secret, $id);
        if($new_mac == $_POST['XSRFPreventionToken'])
            return true;
        else
            return false;
    }
}

?>