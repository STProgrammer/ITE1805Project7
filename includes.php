<?php
    spl_autoload_register(function ($class_name) {
    require_once "classes/" .$class_name . '.class.php';
    });

    require_once '../vendor/autoload.php';

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Session\Session;

    $request = Request::createFromGlobals();
    if($request->hasPreviousSession()) $session = $request->getSession();
    else $session = new Session();

    // Twig templates
    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    $db = Db::getDBConnection();
    if ($db==null) {
        echo $twig->render('error.twig', array('msg' => 'Unable to connect to the database!'));
        die();  // Abort further execution of the script
}
?>