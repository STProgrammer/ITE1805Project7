<?php

require_once '../includes.php';

define('FILENAME_TAG', 'image');

//Håndterer login
require_once "../login.php";

$archive = new FileArchive($db, $request, $session);


    // sjekk om en fil er sendt inn OG personen er innlogget
    if($request->request->has('post_file') && $session->get('loggedin') && $user->verifyUser($request))
    {
        if (XsrfProtection::verifyMac("File upload")) {
            $id = $archive->save($user->getUsername());
            $get_info = "fileupload=1";
            if ($id == 0) {
                header("Location: ../?" . $get_info);
                exit();
            }
            else {
                header("Location: ../file-details/?id=". $id . "&" . $get_info);
                exit();
            }
        }
    }

    // Add catalog
    elseif($request->request->has('post_catalog') && $session->has('loggedin') && $user->verifyUser($request))
    {
        if (XsrfProtection::verifyMac("Catalog upload")) {
            $id = $archive->addCatalog($user->getUsername());
            $get_info = "addcatalog=1";
            header("Location: ../catalog/?id=". $id . "&" . $get_info);
            exit();
        }
    }


    elseif($request->query->has('fileupload') or $request->query->has('addcatalog')) {
        echo $twig->render('fileupload.twig', array('user' => $user,
            'session' => $session, 'rel' => $rel));
    }

    // vis formen
    else {
        if ($session->get('loggedin') && $user = $session->get('User')) {
            $catalogsList = $archive->getCatalogsByOwner($user->getUsername());
            echo $twig->render('fileupload.twig', array('user' => $user,
                'rel' => $rel, 'catalogsList' => $catalogsList));
        }
        else {
            echo $twig->render('fileupload.twig', array('user' => $user, 'rel' => $rel));
        }
    }
?>