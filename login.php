<?php
//logg ut
if ($request->request->has('logout')) {
    $session->clear('loggedin');
    header("Location: ". $request->server->get('REQUEST_URI'));
    exit();
}

$user = new User($db);


if ($request->request->has('login')) {
    if ($user->loggedIn() && $user->verifyUser()) {
        header("Location: ".$request->server->get('REQUEST_URI'));
        exit();
    }
    else {
        $get_info = "?loginfail=1";
        header("Location: ./login".$get_info);
        exit();
    }
}
?>