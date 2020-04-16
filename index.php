<?php

    require_once "includes.php";

    error_reporting(E_ALL);
    define('FILNAVN_TAG', 'bildeFil');

    //Håndterer login
    require_once "login.php";

    // opprett nytt filarkiv
    $archive = new FileArchive($db);

    //Vis fil
    if($request->query->has('id') && ctype_digit($request->query->get('id')))
    {
        $id = $request->query->getInt('id');
        $file = $archive->getFileObject($id);
        if ($file) {
            if ($file->getAccess() == 0) {
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
    // vis oversikten
    else {
        $overview = $archive->visOversikt();
        $notification = $archive->getNotification();
        echo $twig->render('index.twig', array('files' => $overview, 'user' => $user,
            'notification' => $notification, 'script' => dirname($_SERVER['PHP_SELF'])));
    }

?>