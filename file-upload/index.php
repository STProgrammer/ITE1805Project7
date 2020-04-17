<?php

require_once '../includes.php';

define('FILENAME_TAG', 'image');

//Håndterer login
require_once "../login.php";

$archive = new FileArchive($db, $request, $session);

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
        echo $twig->render('fileupload.twig', array('user' => $user,
            'session' => $session, 'rel' => $rel));
    }

    // vis formen
    else {
        $mac = XsrfProtection::getMac("File upload");
        echo $twig->render('fileupload.twig', array('user' => $user,
            'mac' => $mac, 'rel' => $rel));
    }
?>