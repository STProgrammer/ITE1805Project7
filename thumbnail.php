<?php

require_once "includes.php";

$archive = new FileArchive($db, $request, $session, $twig);



//Vis thumbnail
if(ctype_digit($request->query->get('id')))
{
    $id = $request->query->getInt('id');
    if(!$archive->showThumbnail($id)) {
        echo $twig->render('index.twig', array('user' => $user,
            'session' => $session, 'rel' => $rel));
    }
}
