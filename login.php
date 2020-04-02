<?php
//logg ut
if (isset($_POST['logout'])) {
    unset($_SESSION['innlogget']);
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit();
}

$db = Db::getDBConnection();
if ($db==null) {
    echo $twig->render('error.twig', array('msg' => 'Unable to connect to the database!'));
    die();  // Abort further execution of the script
}
$user = new User($db);

if (isset($_POST['login'])) {
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