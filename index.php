<?php

    require_once "includes.php";

    define('FILENAME_TAG', 'image');

    //Håndterer login
    require_once "login.php";

    // opprett nytt filarkiv
    $archive = new FileArchive($db, $request, $session);


/* Denne Twig funksjonen er tatt fra https://stackoverflow.com/questions/61407758/how-to-change-one-value-in-get-by-clicking-a-link-or-button-from-twig-with/61407993#61407993 */
    $twig->addFunction(new \Twig\TwigFunction('get_page_url', function($query = [], $append = true) {
    $tmp = $append ? $_GET : [];
    foreach($query as $key => $value) $tmp[$key] = $value;

    return '?' . http_build_query($tmp);
    }));

    //Get the page number for pagination, metoden er delvis tatt fra https://www.myprogrammingtutorials.com/create-pagination-with-php-and-mysql.html
    if (!($pageno = $request->query->getInt('pageno')) || $pageno < 1) {
        $pageno = 1;
    }
    $nrOfElementsPerPage = 40;
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
            $searchQuery = $request->query->get('query');
            $elements = $archive->searchFiles($searchQuery);
            if (!$session->get('loggedin')) {
                $elements = $archive->getOnlyPublicFiles($files);
            }
            $sizeOfList = sizeof($elements);
            $totalPages = ($sizeOfList == 0) ? 1 : ceil($sizeOfList / $nrOfElementsPerPage);
            $pagination = range(1, $totalPages, 1);
            $files = array_slice($elements, $offset, $nrOfElementsPerPage);
            echo $twig->render('index.twig', array('elements' => $elements, 'user' => $user,
                'session' => $session, 'request' => $request, 'rel' => $rel, 'pagination' => $pagination));
        }

    //Tag search
    elseif($request->query->get('tag'))
    {
        $tag = $request->query->get('tag');
        $elements = $archive->searchFilesByTag($tag);
        if (!$session->get('loggedin')) {
            $files = $archive->getOnlyPublicFiles($files);
        }
        $sizeOfList = sizeof($elements);
        $totalPages = ($sizeOfList == 0) ? 1 : ceil($sizeOfList / $nrOfElementsPerPage);
        $pagination = range(1, $totalPages, 1);
        $elements = array_slice($elements, $offset, $nrOfElementsPerPage);
        echo $twig->render('index.twig', array('elements' => $elements, 'user' => $user,
            'session' => $session, 'request' => $request, 'rel' => $rel, 'pagination' => $pagination));
    }

    //Multiple tags search
    elseif($request->query->get('search') == "tagssearch")
    {
        $tagsStr = $request->query->get('search');
        if ($request->query->get('andcondition') == 1) {
            $elements = $archive->searchByTagsWithAndCondition($tagsStr);
        } else { $elements = $archive->searchByTagsWithOrCondition($tagsStr);}
        if (!$session->get('loggedin')) {
            $elements = $archive->getOnlyPublicFiles($elements);
        }
        $sizeOfList = sizeof($elements);
        $totalPages = ($sizeOfList == 0) ? 1 : ceil($sizeOfList / $nrOfElementsPerPage);
        $pagination = range(1, $totalPages, 1);
        $elements = array_slice($elements, $offset, $nrOfElementsPerPage);
        echo $twig->render('index.twig', array('elements' => $elements, 'user' => $user,
            'session' => $session, 'request' => $request, 'rel' => $rel, 'pagination' => $pagination));
    }

    elseif (ctype_digit($request->query->get('catalogid'))) {

        $catalogId = $request->query->getInt('catalogid');
        $catalogId = $catalogId == 0 ? 1: $catalogId;
        $catalogPath = $archive->getCatalogPath($catalogId);

        if(!$archive->showCatalog($catalogId)) {
            echo $twig->render('index.twig', array('user' => $user,
                'session' => $session, 'rel' => $rel));
        } else {
            $elements = $archive->getOverview($catalogId, $offset, $nrOfElementsPerPage);
            echo $twig->render('index.twig', array('elements' => $elements, 'user' => $user,
                'session' => $session, 'request' => $request, 'rel' => $rel, 'catalogPath' => $catalogPath));
        }
    }

    // vis oversikten
    else {
        $totalPages = $archive->totalNrOfPages($nrOfElementsPerPage, 1);
        $pagination = range(1, $totalPages, 1);
        $elements = $archive->getOverview(1, $offset, $nrOfElementsPerPage);

        echo $twig->render('index.twig', array('elements' => $elements, 'user' => $user,
            'session' => $session, 'request' => $request, 'rel' => $rel, 'pagination' => $pagination));
    }
?>