<?php

require_once '../../includes.php';

require_once "../../login.php";

$regUser = new RegisterUser($db, $request, $session);

//Since only loggedin users can edit data, we check if user is logged in, verify user etc. all at once
if ($request->query->has('username') && ($user = $session->get('User'))
    && $user->verifyUser($request) && $session->get('loggedin')) {

    $username = $user->getUsername();
    $userData = $regUser->getUserData($username);


    // Admin can delete user and edit user information
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


    // User change password
    if ($request->request->has('Delete_user') && $request->request->get('Delete_user') == "Delete user") {
        //is  admin or is User
        if ($isAdmin or $isUser) {
            if (XsrfProtection::verifyMac("Delete")) {
                $regUser->deleteUser($username);
                $get_info = "?userdeleted=1";
                header("Location: ../" . $get_info);
                exit();
            }
        }
    } //End delete user

    elseif($request->request->get('edit_user') == "Edit") {
        if (($isUser || $isAdmin) && XsrfProtection::verifyMac("Edit user information")) {
            $regUser->editUser($username);
            $get_info = "username=" . $username . "&useredited=1";
            header("Location: ../?" . $get_info);
            exit();
        }

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