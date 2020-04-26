<?php

require_once '../../includes.php';

require_once '../../login.php';


$archive = new FileArchive($db, $request, $session, $twig);

if (ctype_digit($request->query->get('id')) && $user = $session->get('User')) {

    $id = $request->query->getInt('id');
    $catalog = $archive->getCatalogObject($id);


    // Check if user owns the profile. Only owner of the profile can edit the profile.
    // Admin can delete information of profile, but can't edit it.
    $isOwner = false;  //isOwner controls if the user owns the catalog or not
    if ($session->get('loggedin') && $user->verifyUser($request)) {
        if ($user->getUsername() == $catalog->getOwner()) {  //check if user owns the catalog
            $isOwner = true;
            $catalogsList = $archive->getCatalogsByOwner($user->getUsername());
        }
    }

    //If not owner, quit it
    if (!$isOwner) {
        header("Location: ../");
        exit();
    }

    elseif ($request->request->get('edit_catalog') == "Edit") {
        if ($isOwner && XsrfProtection::verifyMac("Edit catalog")) {
            $archive->editCatalog($id);
            $get_info = "id=" . $id . "&catalogedited=1";
            header("Location: ../?" . $get_info);
            exit();
        }
    }

    else {
        echo $twig->render('catalog-edit.twig', array('catalog' => $catalog,
            'request' => $request, 'session' => $session, 'rel' => $rel, 'isOwner' => $isOwner,
            'xsrfMac' => $xsrfMac, 'user' => $user));
    }

}

else {
    $get_info = "id=" . $id ;
    header("Location: ../?" . $get_info);
    exit();
}

?>