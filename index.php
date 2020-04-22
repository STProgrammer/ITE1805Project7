<?php

    require_once "includes.php";

    define('FILENAME_TAG', 'image');

    //Håndterer login
    require_once "login.php";

    // opprett nytt filarkiv
    $archive = new FileArchive($db, $request, $session, $twig);

    //Get the page number for pagination, metoden er delvis tatt fra https://www.myprogrammingtutorials.com/create-pagination-with-php-and-mysql.html
    if (!($pageno = $request->query->getInt('pageno')) || $pageno < 1) {
        $pageno = 1;
    }

    $nrOfElementsPerPage = 4;
    $offset = ($pageno-1) * $nrOfElementsPerPage;

    //Vis fil
    if(ctype_digit($request->query->get('id')))
    {
        $id = $request->query->getInt('id');
        if(!$archive->showFile($id)) {
            echo $twig->render('index.twig', array('user' => $user,
                'session' => $session, 'rel' => $rel));
        }
    }




    //Search made
    elseif($request->query->get('search') == "search")
        {
            $searchQuery = filter_input(INPUT_GET, 'query', FILTER_SANITIZE_STRING);
            $files = $archive->searchFiles($searchQuery);
            if (!$session->get('loggedin')) {
                $files = $archive->getOnlyPublicFiles($files);
            }
            $sizeOfList = sizeof($files);
            $totalPages = ($sizeOfList == 0) ? 1 : ceil($sizeOfList / $nrOfElementsPerPage);
            $pagination = range(1, $totalPages, 1);
            $files = array_slice($files, $offset, $nrOfElementsPerPage);
            echo $twig->render('index.twig', array('elements' => $files, 'user' => $user,
                'session' => $session, 'request' => $request, 'rel' => $rel, 'pagination' => $pagination));
        }

    //Tag search
    elseif($request->query->get('tag'))
    {
        $tag = filter_input(INPUT_GET, 'tag', FILTER_SANITIZE_STRING);
        $files = $archive->searchFilesByTag($tag);
        if (!$session->get('loggedin')) {
            $files = $archive->getOnlyPublicFiles($files);
        }
        $files = array_slice($files, $offset, $nrOfElementsPerPage);
        $pagination = range(1, sizeof($files), 1);
        echo $twig->render('index.twig', array('elements' => $files, 'user' => $user,
            'session' => $session, 'request' => $request, 'rel' => $rel, 'pagination' => $pagination));
    }

    //Multiple tags search
    elseif($request->query->get('search') == "tagssearch")
    {
        $tagsStr = filter_input(INPUT_GET, 'tags', FILTER_SANITIZE_STRING);
        $files = $archive->searchByMultipleTags($tagsStr);
        if (!$session->get('loggedin')) {
            $files = $archive->getOnlyPublicFiles($files);
        }
        $files = array_slice($files, $offset, $nrOfElementsPerPage);
        $pagination = range(1, sizeof($files), 1);
        echo $twig->render('index.twig', array('files' => $files, 'user' => $user,
            'session' => $session, 'request' => $request, 'rel' => $rel, 'pagination' => $pagination));
    }

    elseif (ctype_digit($request->query->get('catalogid'))) {
        $catalogId = $request->query->getInt('catalogid');
        $catalogId = $catalogId == 0 ? 1: $catalogId;
        $catalog = $archive->getCatalogObject($catalogId);

        if(!$archive->showCatalog($catalogId)) {
            echo $twig->render('index.twig', array('user' => $user,
                'session' => $session, 'rel' => $rel));
        } else {
            $elements = $archive->getOverview($catalogId, $offset, $nrOfElementsPerPage);
            echo $twig->render('index.twig', array('elements' => $elements, 'user' => $user,
                'session' => $session, 'request' => $request, 'rel' => $rel));
        }
    }

    // vis oversikten
    else {
        $totalPages = $archive->totalNrOfPages($nrOfElementsPerPage);
        $pagination = range(1, $totalPages, 1);
        $elements = $archive->getOverview(1, $offset, $nrOfElementsPerPage);

        echo $twig->render('index.twig', array('elements' => $elements, 'user' => $user,
            'session' => $session, 'request' => $request, 'rel' => $rel, 'pagination' => $pagination));
    }
?>