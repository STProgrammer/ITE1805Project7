<?php

    $homedir = __DIR__ . '/';

    //Generate relative path string "../"
    $rel = substr( dirname($_SERVER['PHP_SELF']), strrpos(dirname($_SERVER['PHP_SELF']),"ite1805project7"));
    $rel = str_replace('/', '../', $rel);
    $rel = preg_replace('~[^\.\.\/]*~', '', $rel);



    spl_autoload_register(function ($class_name) {
        $homedir = __DIR__ . '/';
        require_once ($homedir . "classes/" .$class_name . '.class.php');
    });

    require_once $homedir . 'vendor/autoload.php';

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Session\Session;
    use Symfony\Bridge\Twig;

    $request = Request::createFromGlobals();

    if($request->hasPreviousSession()) $session = $request->getSession();
    else $session = new Session();
    $session->start();

    error_reporting(E_ALL);

    // Twig templates
    $loader = new \Twig\Loader\FilesystemLoader($homedir . 'templates');
    $twig = new \Twig\Environment($loader, ['debug' => true]);
    $twig->addExtension(new \Twig\Extension\DebugExtension());

    $twig->addFunction(new \Twig\TwigFunction('getMac', function($action) {
        return XsrfProtection::getMac($action);
    }));

    $db = Db::getDBConnection();
    if ($db==null) {
        echo $twig->render('error.twig', array('msg' => 'Unable to connect to the database!'));
        die();  // Abort further execution of the script
    }
?>