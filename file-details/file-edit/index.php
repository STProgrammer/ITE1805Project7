<?php

require_once '../../includes.php';

define('FILENAME_TAG', 'image');

require_once '../../login.php';


$archive = new FileArchive($db, $request, $session, $twig);

if (ctype_digit($request->query->get('id')) && $user = $session->get('User')) {

    $fileId = $request->query->getInt('id');
    $file = $archive->getFileObject($fileId);
    $user = $session->get('User');



    // Check if user owns the file. Only owner of the file can edit the file.
    // Admin can delete files, but can't edit files
    $isOwner = false;  //isOwner controls if the user owns the file or not
    if ($session->get('loggedin') && $user->verifyUser($request)) {
        if ($user->getUsername() == $file->getOwner()) {  //check if user owns the file
            $isOwner = true;
            $catalogsList = $archive->getCatalogsByOwner($user->getUsername());
        }
    }

    //If not owner, quit it
    if (!$isOwner) {
        header("Location: ../");
        exit();
    }

    elseif ($request->request->get('edit_file') == "Edit") {
        if ($isOwner && XsrfProtection::verifyMac("Edit file")) {
            $archive->editFile($fileId);
            $get_info = "id=" . $fileId . "&fileedited=1";
            header("Location: ../?" . $get_info);
            exit();
        }
    }

    else {
        $mac = XsrfProtection::getMac("Edit file");
        echo $twig->render('file-edit.twig', array('file' => $file,
            'request' => $request, 'session' => $session, 'rel' => $rel, 'isOwner' => $isOwner,
            'mac' => $mac, 'user' => $user, 'catalogsList' => $catalogsList));
    }

}

else {
    $get_info = "id=" . $id ;
    header("Location: ../?" . $get_info);
    exit();
}

?>