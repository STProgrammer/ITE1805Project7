<?php


public class XsrfProtection
{
    public static function getMac($action_name) {
        $key = "sha1";
        $secret = "g45jf722e";
        return hash_hmac($key, $action_name + $secret, $_GLOBAL['PHP_SESSIONID'])
    }

    public static function verifyMac($action_name) : bool {
        $key = "sha1";
        $secret = "g45jf722e";
        $new_mac = hash_hmac($key, $action_name + $secret, $_GLOBAL['PHP_SESSIONID']);
        if($new_mac == $_POST['XSRFPreventionToken']) return true;
        else return false;
    }
}