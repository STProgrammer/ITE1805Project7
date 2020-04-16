<?php
spl_autoload_register(function ($class_name) {
    require_once "../classes/" .$class_name . '.class.php';
});

require_once '../vendor/autoload.php';
/*
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Session\Session;

    $request = Request::createFromGlobals();*/
    @session_start();

$db = Db::getDBConnection();

if ($db==null) {
    echo $twig->render('error.twig', array('msg' => 'Unable to connect to the database!'));
    die();  // Abort further execution of the script
}

// Twig templates
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

error_reporting(E_ALL);

//logg ut
if (isset($_POST['logout'])) {
    unset($_SESSION['loggedin']);
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit();
}

$user = new User($db);

if (isset($_GET['loginfail'])) {
    echo $twig->render('login.twig', array('user' => $user, 'fail' => true));
}

else {
    if ($user->loggedIn() && $user->verifyUser()) {
        header("Location: ..");
        exit();
    }
    elseif (isset($_POST['login'])) {
        if ($user->loggedIn() && $user->verifyUser()) {
            header("Location: ..");
            exit();
        } else {
            echo $twig->render('login.twig', array('user' => $user, 'fail' => true));
        }
    }
    else {
        echo $twig->render('login.twig', array('user' => $user));
    }
}
?>