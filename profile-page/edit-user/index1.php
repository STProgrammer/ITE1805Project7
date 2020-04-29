<?php

require_once '../../includes.php';

require_once '../../login.php';

$archive = new FileArchive($db, $request, $session, $twig);
$usr = new RegisterUser($db, $request, $session);

if (ctype_digit($request->query->get('id')) && $user = $session->get('User')) {

    $id = $request->query->getInt('id');
    $users = $request->query->get('username');

    //  Only user can edit information.
    // Admin can delete user, but can't edit user information
    $isUser = false;
    if ($session->get('loggedin') && $user->verifyUser($request)) {
        $isUser = true;
        $userList = $usr -> getAllUsers($user->getUsername());
    }

    //If not user, quit it
    if (!$isUser) {
        header("Location: ../");
        exit();

    } elseif ($request->request->get('edit_user') == "Edit") {
        if ($isUser && XsrfProtection::verifyMac("Edit user")) {
            $username = $user->getUserName();
            $usr -> editUser($username);
            $get_info = "&useredited=1";
            header("Location: ../?" . $get_info);
            exit();
        }
    } else {
        echo $twig->render('user-edit.twig', array('users' => $users,
            'request' => $request, 'session' => $session, 'rel' => $rel, 'isUser' => $isUser,
            'xsrfMac' => $xsrfMac, 'user' => $user));
    }
}
else {
    $get_info = "&useredited=1";
    header("Location: ../?" . $get_info);
    exit();
}



