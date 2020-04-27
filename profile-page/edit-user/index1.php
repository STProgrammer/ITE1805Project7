<?php

require_once '../../includes.php';

require_once '../../login.php';

$archive = new FileArchive($db, $request, $session, $twig);

if (ctype_digit($request->query->get('id')) && $user = $session->get('User')) {

    $id = $request->query->getInt('id');
    $username = $request->query->get('username');

    //  Only user can edit information.
    // Admin can delete user, but can't edit user information
    $isUser = false;
    if ($session->get('loggedin') && $user->verifyUser($request)) {
        $isUser = true;
        User :: getAllUsers($username);
    }

    //If not user, quit it
    if (!$isUser) {
        header("Location: ../");
        exit();
    } elseif ($request->request->get('edit_user') == "Edit") {
        if ($isUser && XsrfProtection::verifyMac("Edit user")) {
            RegisterUser::editUser($user);
            $get_info = "&useredited=1";
            header("Location: ../?" . $get_info);
            exit();
        }
    } else {
        echo $twig->render('user-edit.twig', array('username' => $username,
            'request' => $request, 'session' => $session, 'rel' => $rel, 'isUser' => $isUser,
            'xsrfMac' => $xsrfMac, 'user' => $user));
    }
}
else {
    $get_info = "&useredited=1";
    header("Location: ../?" . $get_info);
    exit();
}



