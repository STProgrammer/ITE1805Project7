<?php
//logg ut
if (isset($_POST['logout'])) {
    unset($_SESSION['loggedin']);
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit();
}

$user = new User($db);


if (isset($_POST['login'])) {
    if ($user->loggedIn() && $user->verifyUser()) {
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