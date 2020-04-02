<?php

    spl_autoload_register(function ($class_name) {
        require_once "classes/" .$class_name . '.class.php';
    });
    session_start();
    require_once '../vendor/autoload.php';

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    error_reporting(E_ALL);
    define('FILNAVN_TAG', 'bildeFil');

    //Håndterer login
    require_once "login.php";

    // opprett nytt filarkiv
    $arkiv = new FileArchive($db, $twig);



    if(isset($_GET['id']) && ctype_digit($_GET['id']))
    {
        $id = intval($_GET['id']);
        $arkiv->visFil($id);
    }

    else
    {
        // sjekk om en fil er sendt inn OG personen er innlogget
        if(isset($_POST['post_file']) && $user->loggedIn())
        {
            $arkiv->save();
            $get_info = "?fileupload=1";
            header("Location: ".$_SERVER['REQUEST_URI']."?fileupload=1");
            exit();
        }

        elseif(isset($_GET['fileupload'])) {
            $notification['strHeader'] = $_SESSION['strHeader'];
            $notification['strMessage'] = $_SESSION['strMessage'];
            echo $twig->render('index.twig', array('user' => $user,
                'notification' => $notification));
        }

        // vis oversikten
        else {
            $oversikt = $arkiv->visOversikt();
            $notification = $arkiv->getNotification();
            echo $twig->render('index.twig', array('filene' => $oversikt, 'user' => $user,
                'notification' => $notification));
        }

    }

?>