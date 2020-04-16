<?php
    spl_autoload_register(function ($class_name) {
        require_once "../classes/" .$class_name . '.class.php';
    });

    require_once '../vendor/autoload.php';
    /*
        use Symfony\Component\HttpFoundation\Request;
        use Symfony\Component\HttpFoundation\Session\Session;

        $request = Request::createFromGlobals();
        if($request->hasPreviousSession()) $session = $request->getSession();
        else $session = new Session();*/

    @session_start();

    $db = Db::getDBConnection();
    if ($db==null) {
        echo $twig->render('error.twig', array('msg' => 'Unable to connect to the database!'));
        die();  // Abort further execution of the script
    }


    require_once '../login.php';

    // Twig templates
    $loader = new \Twig\Loader\FilesystemLoader('../templates');
    $twig = new \Twig\Environment($loader);

    error_reporting(E_ALL);

    error_reporting(E_ALL);
    define('FILNAVN_TAG', 'bildeFil');


    $archive = new FileArchive($db);
    $user = new User($db);

    if(isset($_GET['id']) && ctype_digit($_GET['id']))
    {
        $id = intval($_GET['id']);
        $file = $archive->getFileObject($id);
        $notification = $archive->getNotification();
        echo $twig->render('file-details.twig', array('file' => $file, 'user' => $user,
            'notification' => $notification));
    }
    else {
        header("Location: .." );
        exit();
    }

?>