<?php

require_once '../includes.php';

define('FILENAME_TAG', 'image');

require_once '../login.php';


    $archive = new FileArchive($db, $request, $session, $twig);

    if(ctype_digit($request->query->get('id'))) {
        $id = $request->query->getInt('id');
        $file = $archive->getFileObject($id);
        $comments = Comment::getComments($db, $id);

        // Check if user owns the file. Only owner of the file can edit the file.
        // Admin can delete files, but can't edit files
        $isOwner = false;  //isOwner controls if the user owns the file or not, this is to avoid repeated checks
        $isAdmin = false;  //isAdmin controls if the user is admin or not, this is to avoid repeated checks
        if ($session->has('User') && $session->get('loggedin')) {
            $user = $session->get('User');
            if ($user->verifyUser($request)) {  //check if user logged in and verify user
                // Since User is verified, all users can post comment. So we can now check if someone posted comment
                if ($request->request->get('comment') == "Comment") {
                    if (XsrfProtection::verifyMac("Post comment")) {
                        $username = $user->getUserName();
                        $fileId = $file->getFileId();
                        Comment::addComment($db, $username, $fileId);
                        $get_info = "?id=" . $id . "&comment=1";
                        header("Location: ." . $get_info);
                        exit();
                    }
                }
                if ($user->isAdmin() == 1) {
                    $isAdmin = true;
                }  //check if user is Admin
                if ($user->getUsername() == $file->getOwner()) {  //check if user owns the file
                    $isOwner = true;
                }
            } //End if user verified
        } // End checking file owner and admin
        // User verified, check if delete comment
        if ($request->request->get('Delete_comment') == "Delete comment" && ctype_digit($request->request->get('commentid'))) {
            //Get the comment id;
            $commentId = $request->request->getInt('commentid');
            //Check if user is owner of comment or admin
            if (Comment::checkOwner($db, $user->getUserName(), $commentId) or $isAdmin) {
                //Xsrf check
                if (XsrfProtection::verifyMac("Delete comment")) {
                    $username = $user->getUserName();
                    $fileId = $file->getFileId();
                    Comment::deleteComment($db, $commentId);
                    $get_info = "?id=" . $id . "&deletecomment=1";
                    header("Location: ." . $get_info);
                    exit();
                }
            }
        }

        // File delete submitted
        elseif ($request->request->has('Delete_file') && $request->request->get('Delete_file') == "Delete file") {
            //is owner or admin
            if ($isOwner or $isAdmin) {
                if (XsrfProtection::verifyMac("Delete file")) {
                    $archive->deleteFile($id);
                    $get_info = "?filedeleted=1";
                    header("Location: ../" . $get_info);
                    exit();
                }
            }
        } //End delete file
        // just show the details
        else {
            echo $twig->render('file-details.twig', array('file' => $file, 'user' => $user,
                'request' => $request, 'session' => $session, 'rel' => $rel, 'isOwner' => $isOwner,
                'xsrfMac' => $xsrfMac, 'comments' => $comments));
        }
    }

    // No file ID on GET, file doesn't exist, go back to homepage.
    else {
        header("Location: .." );
        exit();
    }

?>