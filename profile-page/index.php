
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
    // Only owner of the profile can edit the profile.
    // Admin can delete information of profile, but can't edit profile
    $isOwner = false;  //isOwner controls if the user owns the profile or not, this is to avoid repeated checks
    $isAdmin = false;  //isAdmin controls if the user is admin or not, this is to avoid repeated checks
    if ($session->has('User') && $session->get('loggedin')) {
        $user = $session->get('User');
        if ($user->verifyUser($request)) {  //check if user logged in and verify user
            if ($user->isAdmin() == 1) {
                $isAdmin = true;
            }  //check if user is Admin
            if ($user->getUsername() == $catalog->getOwner()) {
                $isOwner = true;
            }
        } //End if user verified
    } // End checking owner and admin

    // User delete submitted
    if ($request->request->has('Delete_user') && $request->request->get('Delete_user') == "Delete user") {
        //is owner or admin
        if ($isOwner or $isAdmin) {
            if (XsrfProtection::verifyMac("Delete")) {
                $archive->deleteUser($username);
                $get_info = "?userdeleted=1";
                header("Location: ../" . $get_info);
                exit();
            }
        }
    } //End delete user

    // just show the details
    else {
        echo $twig->render('profile-page.twig', array('catalog' => $catalog, 'user' => $user,
            'request' => $request, 'session' => $session, 'rel' => $rel, 'isOwner' => $isOwner,
            'xsrfMac' => $xsrfMac));
    }
}

else {
    header("Location: .." );
    exit();
}

?>