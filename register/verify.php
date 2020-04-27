<?php


require_once('../includes.php');

$reguser = new RegisterUser($db, $request, $session);

$userData = array();

if(isset($userData)){
    $query = "select * from Users where verified ='0' and  verCode = 'id' ";

    if($query){

        $request->query("update Users set verified = 1 where email = 'email' ");
        $msg = 'Your account has been activated successfully. You can now login.';
    }
    else{
        $msg = 'error.';
    }
    echo $msg;
    $reguser->registerUser($userData);
}


?>