<?php

require_once '../../includes.php';

define('FILENAME_TAG', 'image');

require_once '../../login.php';

require_once "../../fetchtags.php";

$archive = new FileArchive($db, $request, $session);

if (ctype_digit($request->query->get('id')) && ($user = $session->get('User'))
    && $user->verifyUser($request) && $session->get('loggedin')) {

    $fileId = $request->query->getInt('id');
    $file = $archive->getFileObject($fileId);
    $user = $session->get('User');



    // Check if user owns the file. Only owner of the file can edit the file.
    // Admin can delete files, but can't edit files
    $isOwner = false;  //isOwner controls if the user owns the file or not
    if ($user->getUsername() == $file->getOwner()) {  //check if user owns the file
            $isOwner = true;
    }

    //If not owner, quit it
    else {
        header("Location: ../");
        exit();
    }

    if ($request->request->get('edit_file') == "Edit") {
        if ($isOwner && XsrfProtection::verifyMac("Edit file")) {
            $archive->editFile($fileId);
            $get_info = "id=" . $fileId . "&fileedited=1";
            header("Location: ../?" . $get_info);
            exit();
        }
    }

    else {
        $catalogsList = $archive->getCatalogsByOwner($user->getUsername());
        echo $twig->render('file-edit.twig', array('file' => $file,
            'request' => $request, 'session' => $session, 'rel' => $rel, 'isOwner' => $isOwner,
            'user' => $user, 'catalogsList' => $catalogsList, 'archive' => $archive));
    }

}

else {
    $get_info = "id=" . $request->query->get('id');
    header("Location: ../?" . $get_info);
    exit();
}

?>