<?php

require_once '../../includes.php';

require_once "../../login.php";

include("process.php");

$regUser = new RegisterUser($db, $request, $session);

//Since only logged in users can edit data, we check if user is logged in, verify user etc. all at once
if ($request->query->has('username') && ($user = $session->get('User'))
    && $user->verifyUser($request) && $session->get('loggedin')) {

    $username = $request->query->get('username');
    $userData = $regUser->getUserData($username);


    // Admin can delete user and change user password
    $isUser = false;
    $isAdmin = false;  //isAdmin controls if the user is admin or not, this is to avoid repeated checks
     //check if user is Admin
    if ($user->isAdmin() == 1) {
        $isAdmin = true;
    }
    if ($user->getUsername() == $username) {
        $isUser = true;
    }

    //Only owner of account or admin can edit, if not owner of account or admin, just exit the page
    if (!$isUser && !$isAdmin) {
        header("Location: .." );
        exit();
    }


    // User delete
    if ($request->request->has('Delete_user') && $request->request->get('Delete_user') == "Delete user") {
        //is  admin or is User
        if ($isAdmin or $isUser) {
            if (XsrfProtection::verifyMac("Delete")) {
                $regUser->deleteUser($username);
                //If the user is not admin, the session should be cleared so the user logout
                if (!$isAdmin) {
                    $session->clear();
                }
                $get_info = "?userdeleted=1";
                header("Location: ../" . $get_info);
                exit();
            } else exit();
        } else exit();
    } //End delete user

    elseif($request->request->get('edit_user') == "Edit") {
        if (($isUser) && XsrfProtection::verifyMac("Edit user information")) {
            $regUser->editUser($username);
            $get_info = "username=" . $username . "&useredited=1";
            header("Location: ../?" . $get_info);
            exit();
        } else exit();
    }


    //Change password
    elseif ($request->request->get('change_password') == "Change") {
        if (($isUser || $isAdmin) && XsrfProtection::verifyMac("change password")) {
            $password = $request->request->get('password');
            //Logout after password change
            if ($regUser->changePassword($password, $username)) {
                $session->clear();
            }
            $get_info = "username=" . $username . "&passchanged=1";
            header("Location: ../?" . $get_info);
            exit();
        } else exit();
    }

    //Change email
    elseif ($request->request->get('change_email') == "Change") {
        if (($isUser) && XsrfProtection::verifyMac("change email")) {
            $email = $request->request->get('email');
            $password = $request->requestget('password');
            //Logout after email change
            if ($regUser->changeEmail($email, $username)) {
                 $session->clear();
            }
            $get_info = "username=" . $username . "&emailchanged=1";
            header("Location: ../?" . $get_info);
            exit();
        } else exit();
    }



    // just show the details
    else {
        echo $twig->render('edit-user.twig', array('userData' => $userData, 'user' => $user,
            'request' => $request, 'session' => $session, 'rel' => $rel, 'isUser' => $isUser));
    }
} else {
    header("Location: ..");
    exit();
}

?>