<?php

require_once '../includes.php';

define('FILENAME_TAG', 'image');

require_once '../login.php';


    $archive = new FileArchive($db, $request, $session);

    if($request->query->has('id') && ctype_digit($request->query->get('id')))
    {
        $id = $request->query->getInt('id');
        $file = $archive->getFileObject($id);
        echo $twig->render('file-details.twig', array('file' => $file, 'user' => $user,
            'request' => $request, 'session' => $session, 'rel' => $rel));
    }
    else {
        header("Location: .." );
        exit();
    }

?>