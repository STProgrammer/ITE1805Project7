<?php
//logg ut
if ($request->request->has('logout')) {
    $session->clear('loggedin');
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit();
}

$user = new User($db);


if ($request->request->has('login')) {
    if ($user->loggedIn()) {
        header("Location: ".$_SERVER['REQUEST_URI']);
        exit();
    }
    else {
        $get_info = "?loginfail=1";
        header("Location: ./login".$get_info);
        exit();
    }
}
?>