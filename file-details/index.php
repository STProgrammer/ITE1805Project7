<?php

require_once '../includes.php';

    define('FILNAVN_TAG', 'bildeFil');

require_once '../login.php';


    $archive = new FileArchive($db);

    if($request->query->has('id') && ctype_digit($request->query->get('id')))
    {
        $id = $request->query->getInt('id');
        $file = $archive->getFileObject($id);
        $notification = $archive->getNotification();
        $uploaded = isset($_GET['fileupload']) ? $_GET['fileupload'] : 0;
        echo $twig->render('file-details.twig', array('file' => $file, 'user' => $user,
            'notification' => $notification, 'uploaded' => $uploaded, 'homepath' => $homepath));
    }
    else {
        header("Location: .." );
        exit();
    }

?>