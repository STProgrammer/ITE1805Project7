<?php

require_once '../includes.php';

//Håndterer login
require_once "../login.php";

$archive = new FileArchive($db, $request, $session, $twig);

if (ctype_digit($request->query->get('id'))) {
    $id = $request->query->getInt('id');
    $username = $request->query->get('username');

    //  Only user can edit information.
    // Admin can delete user, but can't edit user information
    $isUser = false;
    $isAdmin = false;  //isAdmin controls if the user is admin or not, this is to avoid repeated checks
    if ($session->has('User') && $session->get('loggedin')) {
        $user = $session->get('User');
        if ($user->verifyUser($request)) {  //check if user logged in and verify user
            if ($user->isAdmin() == 1) {
                $isAdmin = true;
            }  //check if user is Admin

        } //End if user verified
    } // End checking user and admin

    // User delete submitted
    if ($request->request->has('Delete_user') && $request->request->get('Delete_user') == "Delete user") {
        //is  admin
        if ($isAdmin) {
            if (XsrfProtection::verifyMac("Delete")) {
                $isUser = true;
                RegisterUser::deleteUser($username);
                $get_info = "?userdeleted=1";
                header("Location: ../" . $get_info);
                exit();
            }
        }
    } //End delete user

    // just show the details
    else {
        echo $twig->render('user.twig', array('username' => $username, 'user' => $user,
            'request' => $request, 'session' => $session, 'rel' => $rel, 'isUser' => $isUser,
            'xsrfMac' => $xsrfMac));
    }
} else {
    header("Location: ..");
    exit();
}




