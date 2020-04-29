
<?php

require_once '../includes.php';

define('FILENAME_TAG', 'image');

//Håndterer login
require_once "../login.php";

$archive = new FileArchive($db, $request, $session, $twig);

if($request->query->get('username')){
    $isOwner = false;  //isOwner controls if the user owns this account or not, this is to avoid repeated checks
    $isAdmin = false;  //isAdmin controls if the user is admin or not, this is to avoid repeated checks
    if ($session->has('User') && $session->get('loggedin')) {
        $user = $session->get('User');
        $userData = $regUser->getUserData($username);

        if ($user->verifyUser($request)) {  //check if user logged in and verify user
            if ($user->isAdmin() == 1) {
                $isAdmin = true;
            }  //check if user is Admin
            if ($user->getUserName() == $userData['username']) {
                $isOwner = true;
            }
        } //End if user verified
    } // End checking owner and admin

    if (ctype_digit($request->query->get('id'))){

        $id = $request->query->getInt('id');
        $catalog = $archive->getCatalogObject($id);
        // Check if user owns the file. Only owner of the file can edit the file.
        // Admin can delete files, but can't edit files
        $isOwner = false;  //isOwner controls if the user owns the file or not, this is to avoid repeated checks
        $isAdmin = false;  //isAdmin controls if the user is admin or not, this is to avoid repeated checks

        if ($session->has('User') && $session->get('loggedin')) {
            $user = $session->get('User');
            if ($user->verifyUser($request)) {  //check if user logged in and verify user
                if ($user->isAdmin() == 1) {
                    $isAdmin = true;
                }  //check if user is Admin
                if ($user->getUsername() == $catalog->getOwner()) {  //check if user owns the file
                    $isOwner = true;
                }
            } //End if user verified
        } // End checking file owner and admin

        // Catalog delete submitted
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
    }

    echo $twig->render('profile-page.twig', array('user' => $user,'catalog' => $catalog,
            'request' => $request, 'session' => $session, 'rel' => $rel, 'isOwner' => $isOwner,
        'isAdmin'=>$isAdmin, 'xsrfMac' => $xsrfMac));
}

else {
    header("Location: .." );
    exit();
}

?>