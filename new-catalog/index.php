<?php

require_once '../includes.php';

define('FILENAME_TAG', 'image');

//Håndterer login
require_once "../login.php";

$archive = new FileArchive($db, $request, $session);

    // Add catalog
    if($request->request->has('post_catalog') && $session->has('loggedin') && $user->verifyUser($request))
    {
        if (XsrfProtection::verifyMac("Catalog upload")) {
            $id = $archive->addCatalog($user->getUsername());
            $get_info = "addcatalog=1";
            header("Location: ../catalog/?id=". $id . "&" . $get_info);
            exit();
        }
    }


    elseif($request->query->has('addcatalog')) {
        echo $twig->render('newcatalog.twig', array('user' => $user,
            'session' => $session, 'rel' => $rel));
    }

    // vis formen
    else {
        if ($user = $session->get('User')) {
            $catalogsList = $archive->getCatalogsByOwner($user->getUsername());
            echo $twig->render('newcatalog.twig', array('user' => $user,
                'rel' => $rel, 'catalogsList' => $catalogsList));
        }
        else {
            echo $twig->render('newcatalog.twig', array('user' => $user, 'rel' => $rel));
        }
    }
?>