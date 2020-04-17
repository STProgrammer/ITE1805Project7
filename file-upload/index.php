<?php

require_once '../includes.php';

define('FILNAVN_TAG', 'bildeFil');

//Håndterer login
require_once "../login.php";

$archive = new FileArchive($db);

    // sjekk om en fil er sendt inn OG personen er innlogget
    if($request->request->has('post_file') && $session->has('loggedin') && $user->verifyUser($request))
    {
        if (XsrfProtection::verifyMac("File upload")) {
            $id = $archive->save($user->getUsername());
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
    elseif($request->query->has('fileupload')) {
        $notification = $archive->getNotification();
        echo $twig->render('fileupload.twig', array('user' => $user,
            'notification' => $notification, 'script' => $homedir));
    }

    // vis formen
    else {
        $mac = XsrfProtection::getMac("File upload");
        echo $twig->render('fileupload.twig', array('user' => $user,
            'mac' => $mac, 'homepath' => $homepath));
    }
?>