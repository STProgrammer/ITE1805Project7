<?php

require_once '../includes.php';

//Håndterer login
require_once "../login.php";

$archive = new FileArchive($db, $request, $session, $twig);
$password = new RegisterUser($db, $request, $session);

if(ctype_digit($request->query->get('id'))) {
    $id = $request->query->getInt('id');
    $passwords = $request->query->get('password');

    $isAdmin = false;  //isAdmin controls if the user is admin or not, this is to avoid repeated checks
    if ($session->has('User') && $session->get('loggedin')) {
        $user = $session->get('User');
        if ($user->verifyUser($request)) {  //check if user logged in and verify user
            // Since User is verified, all users can change passwords. So we can now check if someone wants to change their password
            if ($request->request->get('Change_password') == "Change_password") {
                if (XsrfProtection::verifyMac("Change password")) {
                    $username = $user -> getUserName();
                    $password -> changePassword($username);
                    $get_info = "&changepassword=1";
                    header("Location: ." . $get_info);
                    exit();
                }
            }
            if ($user->isAdmin() == 1) {
                $isAdmin = true;
            }  //check if user is Admin

        } //End if user verified
        else {
            echo $twig->render('change-password.twig', array('passwords' => $passwords, 'user' => $user,
                'request' => $request, 'session' => $session, 'rel' => $rel, 'xsrfMac' => $xsrfMac));
        }
    }
    else {
        header("Location: ..");
        exit();
    }
}

?>
