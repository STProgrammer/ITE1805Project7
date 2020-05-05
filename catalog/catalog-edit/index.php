<?php

require_once '../../includes.php';

require_once '../../login.php';


$archive = new FileArchive($db, $request, $session);

if (ctype_digit($request->query->get('id')) && ($user = $session->get('User'))
    && $user->verifyUser($request) && $session->get('loggedin')) {

    $id = $request->query->getInt('id');
    $catalog = $archive->getCatalogObject($id);


    // Check if user owns the catalog. Only owner of the catalog can edit the catalog.
    // Admin can delete catalogs, but can't edit catalogs
    $isOwner = false;  //isOwner controls if the user owns the file or not, this is to avoid repeated checks
    if ($user->getUsername() == $catalog->getOwner()) {
        $isOwner = true;
    } // End checking catalog owner

    //If not owner, quit it
    else {
        header("Location: ../");
        exit();
    }

    if ($request->request->get('edit_catalog') == "Edit") {
        if ($isOwner && XsrfProtection::verifyMac("Edit catalog")) {
            $archive->editCatalog($id);
            $get_info = "id=" . $id . "&catalogedited=1";
            header("Location: ../?" . $get_info);
            exit();
        }
    }

    else {
        $catalogsList = $archive->getCatalogsByOwner($user->getUsername());
        echo $twig->render('catalog-edit.twig', array('catalog' => $catalog,
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