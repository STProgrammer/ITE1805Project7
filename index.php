<?php

    require_once "includes.php";

    error_reporting(E_ALL);
    define('FILNAVN_TAG', 'bildeFil');

    //Håndterer login
    require_once "login.php";

    // opprett nytt filarkiv
    $archive = new FileArchive($db);

    //Vis fil
    if(isset($_GET['id']) && ctype_digit($_GET['id']))
    {
        $id = intval($_GET['id']);
        $file = $archive->getFileObject($id);
        if ($file) {
            if ($file->getAccess() == 1) {
                if($user->loggedIn() && $user->verifyUser()) {
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
        if(isset($_POST['post_file']) && $user->loggedIn() && $user->verifyUser())
        {
            if (XsrfProtection::verifyMac("File upload")) {
                $archive->save($_SESSION['bruker']->getUsername());
                $get_info = "?fileupload=1";
                header("Location: ".$_SERVER['REQUEST_URI']."?fileupload=1");
                exit();
            }
        }

        elseif(isset($_GET['fileupload'])) {
            $notification = $archive->getNotification();
            echo $twig->render('index.twig', array('user' => $user,
                'notification' => $notification));
        }

        // vis oversikten
        else {
            $mac = XsrfProtection::getMac("File upload");
            $overview = $archive->visOversikt();
            $notification = $archive->getNotification();
            echo $twig->render('index.twig', array('files' => $overview, 'user' => $user,
                'notification' => $notification, 'mac' => $mac, 'script' => dirname($_SERVER['PHP_SELF'])));
        }
    }
?>