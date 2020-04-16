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
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

error_reporting(E_ALL);
define('FILNAVN_TAG', 'bildeFil');

//Håndterer login
require_once "../login.php";

$archive = new FileArchive($db);

    // sjekk om en fil er sendt inn OG personen er innlogget
    if(isset($_POST['post_file']) && $user->loggedIn() && $user->verifyUser())
    {
        if (XsrfProtection::verifyMac("File upload")) {
            $id = $archive->save($_SESSION['bruker']->getUsername());
            $get_info = "fileupload=1";
            if ($id == 0) {
                header("Location: ./?" . $get_info);
                exit();
            }
            else {
                header("Location: ../file-details/?id=". $id . "&" . $get_info);
                exit();
            }
        }
    }


    elseif(isset($_GET['fileupload'])) {
        $notification = $archive->getNotification();
        echo $twig->render('fileupload.twig', array('user' => $user,
            'notification' => $notification));
    }

    // vis formen
    else {
        $mac = XsrfProtection::getMac("File upload");
        echo $twig->render('fileupload.twig', array('user' => $user,
            'mac' => $mac, 'script' => dirname($_SERVER['PHP_SELF'])));
    }
?>