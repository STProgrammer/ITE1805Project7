<?php

    require_once "includes.php";


    define('FILENAME_TAG', 'image');

    //Håndterer login
    require_once "login.php";

    // opprett nytt filarkiv
    $archive = new FileArchive($db, $request, $session);

    //Vis fil
    if($request->query->has('id') && ctype_digit($request->query->get('id')))
    {
        $id = $request->query->getInt('id');
        $file = $archive->getFileObject($id);
        if ($file) {
            if ($file->getAccess() == 0) {
                if($user->loggedIn() && $user->verifyUser($request)) {
                    $file->showFile();
                }
            }
            else
                $file->showFile();
        }
        else {
            $notification = $archive->getNotification();
            echo $twig->render('index.twig', array('user' => $user,
                'notification' => $notification, 'rel' => $rel));
        }
    }
    // vis oversikten
    else {
        $overview = $archive->visOversikt();
        echo $twig->render('index.twig', array('files' => $overview, 'user' => $user,
            'session' => $session, 'rel' => $rel));
    }

?>