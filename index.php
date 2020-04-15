<?php

    require_once "includes.php";

    error_reporting(E_ALL);
    define('FILNAVN_TAG', 'bildeFil');

    //Håndterer login
    require_once "login.php";

    // opprett nytt filarkiv
    $archive = new FileArchive($db, $twig);

    //Vis fil
    if(isset($_GET['id']) && ctype_digit($_GET['id']))
    {
        $id = intval($_GET['id']);
        $file = $archive->getFileObject($id);
        if ($file) {
            if ($file->isAccessible() == 0) {
                if($user->loggedIn()) {
                    $file->showFile();
                }
            }
            else
                $file->showFile();
        }
        else {
            $notification = $archive->getNotification();
            echo $twig->render('index.twig', array('user' => $user,
                'notification' => $notification, 'script' => $_SERVER['PHP_SELF']));
        }
    }

    else
    {
        // sjekk om en fil er sendt inn OG personen er innlogget
        if(isset($_POST['post_file']) && $user->loggedIn())
        {
            $archive->save();
            $get_info = "?fileupload=1";
            header("Location: ".$_SERVER['REQUEST_URI']."?fileupload=1");
            exit();
        }

        elseif(isset($_GET['fileupload'])) {
            $notification = $archive->getNotification();
            echo $twig->render('index.twig', array('user' => $user,
                'notification' => $notification));
        }

        // vis oversikten
        else {
            $overview = $archive->visOversikt();
            $notification = $archive->getNotification();
            echo $twig->render('index.twig', array('files' => $overview, 'user' => $user,
                'notification' => $notification, 'script' => dirname($_SERVER['PHP_SELF'])));
        }
    }
?>