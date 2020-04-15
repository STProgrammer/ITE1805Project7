<?php
spl_autoload_register(function ($class_name) {
    require_once "../classes/" .$class_name . '.class.php';
});

require_once '../../vendor/autoload.php';
/*
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Session\Session;

    $request = Request::createFromGlobals();
    if($request->hasPreviousSession()) $session = $request->getSession();
    else $session = new Session();*/

@session_set_cookie_params(0);
@session_start();

// Twig templates
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

error_reporting(E_ALL);

//logg ut
if (isset($_POST['logout'])) {
    unset($_SESSION['loggedin']);
    header("Location: ..");
    exit();
}

$db = Db::getDBConnection();
if ($db==null) {
    echo $twig->render('error.twig', array('msg' => 'Unable to connect to the database!'));
    die();  // Abort further execution of the script
}

$user = new User($db);

if (isset($_GET['loginfail'])) {
    echo $twig->render('login2.twig', array('user' => $user, 'fail' => true));
}

else {
    if ($user->loggedIn()) {
        header("Location: ..");
        exit();
    }
    elseif (isset($_POST['login'])) {
        if ($user->loggedIn()) {
            header("Location: ..");
            exit();
        } else {
            echo $twig->render('login2.twig', array('user' => $user, 'fail' => true));
        }
    }
    else {
        echo $twig->render('login2.twig', array('user' => $user));
    }
}
?>