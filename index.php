<?php

    require_once "includes.php";

    define('FILENAME_TAG', 'image');

    //Håndterer login
    require_once "login.php";

    // opprett nytt filarkiv
    $archive = new FileArchive($db, $request, $session, $twig);

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
            echo $twig->render('index.twig', array('files' => $files, 'user' => $user,
                'session' => $session, 'request' => $request, 'rel' => $rel));
        }

    //Tag search
    elseif($request->query->get('tag'))
    {
        $tag = filter_input(INPUT_GET, 'tag', FILTER_SANITIZE_STRING);
        $files = $archive->searchFilesByTag($tag);
        if (!$session->get('loggedin')) {
            $files = $archive->getOnlyPublicFiles($files);
        }
        echo $twig->render('index.twig', array('files' => $files, 'user' => $user,
            'session' => $session, 'request' => $request, 'rel' => $rel));
    }

    //Multiple tags search
    elseif($request->query->get('search') == "tagssearch")
    {
        $tagsStr = filter_input(INPUT_GET, 'tags', FILTER_SANITIZE_STRING);
        $files = $archive->searchByMultipleTags($tagsStr);
        if (!$session->get('loggedin')) {
            $files = $archive->getOnlyPublicFiles($files);
        }
        echo $twig->render('index.twig', array('files' => $files, 'user' => $user,
            'session' => $session, 'request' => $request, 'rel' => $rel));
    }

    elseif (ctype_digit($request->query->get('catalogid'))) {
        $catalogId = $request->query->getInt('catalogid');
        $catalogId = $catalogId == 0 ? 1: $catalogId;
        $catalog = $archive->getCatalogObject($catalogId);

        if(!$archive->showCatalog($catalogId)) {
            echo $twig->render('index.twig', array('user' => $user,
                'session' => $session, 'rel' => $rel));
        } else {
            $overview = $archive->getFilesOverview($catalogId);
            $catalogs = $archive->getCatalogs($catalogId);
            echo $twig->render('index.twig', array('files' => $overview, 'user' => $user,
                'session' => $session, 'request' => $request, 'catalogs' => $catalogs,
                'catalog' => $catalog, 'rel' => $rel));
        }
    }

    // vis oversikten
    else {
        $overview = $archive->getFilesOverview(1);
        $catalogs = $archive->getCatalogs(1);
        echo $twig->render('index.twig', array('files' => $overview, 'user' => $user,
            'session' => $session, 'request' => $request, 'catalogs' => $catalogs, 'rel' => $rel));
    }
?>