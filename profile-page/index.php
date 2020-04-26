<?php

require_once '../includes.php';

define('FILENAME_TAG', 'image');

//Håndterer login
require_once "../login.php";

$archive = new FileArchive($db, $request, $session, $twig);

if(ctype_digit($request->query->get('id')))
{
    $id = $request->query->getInt('id');
    $catalog = $archive->getCatalogObject($id);
    // Check if user owns the profile. Only owner of the profile can edit the profile.
    // // Admin can delete the information of user, but can't edit it
    $isOwner = false;  //isOwner controls if the user owns the profile or not, this is to avoid repeated checks
    $isAdmin = false;  //isAdmin controls if the user is admin or not, this is to avoid repeated checks
    if ($session->has('User') && $session->get('loggedin')) {
        $user = $session->get('User');
        if ($user->verifyUser($request)) {  //check if user logged in and verify user
            if ($user->isAdmin() == 1) {
                $isAdmin = true;
            }  //check if user is Admin
            if ($user->getUsername() == $catalog->getOwner()) {  //check if user owns the profile
                $isOwner = true;
            }
        } //End if user verified
    } // End checking profile owner and admin

    // Information delete submitted
    if ($request->request->has('Delete_catalog') && $request->request->get('Delete_catalog') == "Delete catalog") {
        //is owner or admin
        if ($isOwner or $isAdmin) {
            if (XsrfProtection::verifyMac("Delete")) {
                $archive->deleteCatalog($id);
                $get_info = "?catalogdeleted=1";
                header("Location: ../" . $get_info);
                exit();
            }
        }
    } //End delete catalog

    // just show the details
    else {
        echo $twig->render('catalog.twig', array('catalog' => $catalog, 'user' => $user,
            'request' => $request, 'session' => $session, 'rel' => $rel, 'isOwner' => $isOwner,
            'xsrfMac' => $xsrfMac));
    }
}

else {
    header("Location: .." );
    exit();
}

?>