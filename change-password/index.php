<?php

/// DECLARE HOMEDIR
$homedir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
    
require_once $homedir.DIRECTORY_SEPARATOR.'includes.php';

//Håndterer login
require_once $homedir.DIRECTORY_SEPARATOR.'login.php';

$archive = new FileArchive($db, $request, $session, $twig);
$password = new RegisterUser($db, $request, $session);

if(ctype_digit($request->query->get('id'))) {
    $id = $request->query->getInt('id');
    if ($user->isAdmin() == 1) {
        $isAdmin = true;
    }
}else{
    $passwords = $request->query->get('password');
    $isAdmin = false;  //isAdmin controls if the user is admin or not, this is to avoid repeated checks
    if ($session->has('User') && $session->get('loggedin')) {
       
        $user = $session->get('User');
        
        //if ($user->verifyUser($request)) {  //check if user logged in and verify user
            // Since User is verified, all users can change passwords. So we can now check if someone wants to change their passwors

            if ($request->request->get('action') == "change_password") {
                /// IF USER SUBMIT CHANGE PASSWORD FORM
                if (XsrfProtection::verifyMac("Change password")) {

                    $username = $user -> getUserName();
                    $password->changePassword($username);
                    $get_info = "index.php?changepassword=1";
                    header("Location: " . $get_info);
                    exit();
                                                          
                }
            }else {
                /// IF USER JUST ENTER THE PAGE, SHOW THEM THE FORM
                echo $twig->render('change-password.twig', array('passwords' => $passwords, 'user' => $user,
                    'request' => $request, 'session' => $session, 'rel' => $rel,
                    'message'=>$session->getFlashBag()->get('message', [])
                    ///'xsrfMac' => $xsrfMac
                ));
            }
    
    }else {
        header("Location: ..");
        exit();
    }
}
?>


