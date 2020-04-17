<?php

    $homedir = __DIR__ . '/';
    $homepath = str_replace("\\", "/", $homedir);
    $homepath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $homepath);

    spl_autoload_register(function ($class_name) {
        $homedir = __DIR__ . '/';
        require_once ($homedir . "classes/" .$class_name . '.class.php');
    });

    require_once $homedir . 'vendor/autoload.php';

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Session\Session;

    $request = Request::createFromGlobals();

    if($request->hasPreviousSession()) $session = $request->getSession();
    else $session = new Session();
    $session->start();

    error_reporting(E_ALL);

    // Twig templates
    $loader = new \Twig\Loader\FilesystemLoader($homedir . 'templates');
    $twig = new \Twig\Environment($loader);

    $db = Db::getDBConnection();
    if ($db==null) {
        echo $twig->render('error.twig', array('msg' => 'Unable to connect to the database!'));
        die();  // Abort further execution of the script
}
?>