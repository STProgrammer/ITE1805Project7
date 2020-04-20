<?php

class FileArchive {
        
        private $db;
        private $request;
        private $session;
        private $twig;
   
        function __construct($db, $request, $session, $twig) {
           
            $this->db = $db;
            $this->request = $request;
            $this->session = $session;
            $this->twig = $twig;
        }

        //* NOTIFY USER
                
        private function NotifyUser($strHeader, $strMessage)
        {
            $this->session->getFlashBag()->add('header', $strHeader);
            $this->session->getFlashBag()->add('message', $strMessage);
        }

        //* END NOTIFY USER

    /////////////////////////////////////////////////////////////////////////////
    /// PRIVATE FUNCTIONS
    /// //////////////////////////////////////////////////////////////////////////

    //* ADD TAGS

        private function addTags(string $tagsStr, int $fileId) : bool {
            $tagsStr = preg_replace("/[^\w\s\,]+/", "", $tagsStr);
            $tagsStr = preg_replace("/\s*,\s*/", ",", $tagsStr);
            $tagsStr = preg_replace("/\,*,\,*/", ",", $tagsStr);
            $tags = explode(',', $tagsStr);
            $tags = array_unique($tags);
            $tags = array_filter($tags);
            try {
                $stmt = $this->db->prepare("INSERT IGNORE INTO Tags (tag) VALUES (:tag);");
                if(is_array($tags)){
                    foreach ($tags as $tag) {
                        $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
                        $stmt->execute();
                    }
                }
                $stmt = $this->db->prepare("INSERT IGNORE INTO FilesAndTags (tag, fileId) VALUES (:tag, :fileid);");
                if(is_array($tags)){
                    foreach ($tags as $tag) {
                        $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
                        $stmt->bindParam(':fileid', $fileId, PDO::PARAM_INT);
                        $stmt->execute();
                    }
                }
            } catch (Exception $e) { $this->NotifyUser("En feil oppstod", $e->getMessage()); return false;}
            return true;

        } //* END ADD TAGS

    private function getTags($id) {
        $stmt = $this->db->prepare("SELECT tag FROM FilesAndTags WHERE fileId = :id;");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $tags = implode(", ", $tags);
        return $tags;
    }

    /////////////////////////////////////////////////////////////////////////////
    /// CATALOG FUNCTIONS
    /// //////////////////////////////////////////////////////////////////////////

        private function isCatalogAccessible($id) : bool {
            if ($id <= 1) {
                return true;
            }
            try
            {
                $stmt = $this->db->prepare("SELECT access, parentId FROM Catalogs WHERE catalogId = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                if($parent = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $parentId = $parent['parentId'];
                    $access = $parent['access'];
                    if ($access == 0) {
                        return false;
                    }
                    else return $this->isCatalogAccessible($parentId);
                }
                else {
                    $this->NotifyUser("Catalog not found", "");
                    return false;
                }
            }
            catch(Exception $e) { $this->NotifyUser("Error 5", $e->getMessage()); }
        } //* END IsCatalogAccessible



        public function showCatalog($id) {
            if (!$this->isCatalogAccessible($id)) {
                if ($this->session->has('User') && $this->session->has('loggedin')) {
                    $user = $this->session->get('User');
                    if ($this->session->get('loggedin') && $user->verifyUser($this->request)) {
                        return true;
                    } else {$this->NotifyUser("Access denied, login to view catalog", ""); return false;}
                } else {$this->NotifyUser("Access denied, login to view catalog", ""); return false;}
            } else {
                return true;
            }
        }  //* END showCatalog



        public function addCatalog(string $owner) : int {
            $this->session->remove('strHeader');
            $this->session->remove('strMessage');

            $catalogname = filter_input(INPUT_POST, 'catalogname', FILTER_SANITIZE_STRING);
            if ($this->request->request->has('access')) $access = 0;
            else $access = 1;
            $parentId = filter_input(INPUT_POST, 'catalogId', FILTER_SANITIZE_NUMBER_INT);
            if (!$this->canUserAddToThisCatalog($parentId)) {
                $this->NotifyUser('User can\'t add to this catalog', '');
                return 0;
            }
            try
            {
                $stmt = $this->db->prepare("INSERT INTO Catalogs (catalogName, parentId, date, owner, access) VALUES (:catalogname, :parentid, now(), :owner, :access);");
                $stmt->bindParam(':catalogname', $catalogname, PDO::PARAM_STR);
                $stmt->bindParam(':parentid', $parentId, PDO::PARAM_INT);
                $stmt->bindParam(':owner', $owner, PDO::PARAM_STR);
                $stmt->bindParam(':access', $access, PDO::PARAM_INT);
                $stmt->execute();
                $id = intval($this->db->lastInsertId());
                $this->NotifyUser("New catalog added !", "");
                return $id;
            }
            catch(Exception $e) { $this->NotifyUser("Error 6", $e->getMessage()); return 0; }
        }  //* END Add catalog


    public function editCatalog($catalogId)
    {
        $catalogName = filter_input(INPUT_POST, 'catalogName', FILTER_SANITIZE_STRING);
        $access = filter_input(INPUT_POST, 'access', FILTER_SANITIZE_NUMBER_INT);
        $parentId = filter_input(INPUT_POST, 'catalogId', FILTER_SANITIZE_NUMBER_INT);
        if ($access == null) $access = 1;
        if (!$this->canUserAddToThisCatalog($parentId)) {
            $this->NotifyUser('User can\'t add to this catalog', '');
            return false;
        }
        try {
            $sth = $this->db->prepare("update Catalogs set catalogName = :catalogName, access = :access, parentId = :parentId where catalogId = :catalogId");
            $sth->bindParam(':catalogId', $catalogId);
            $sth->bindParam(':catalogName', $catalogName);
            $sth->bindParam(':access', $access);
            $sth->bindParam(':parentId', $parentId);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->NotifyUser('Catalog details changed', '');
            } else {
                $this->NotifyUser('Failed to change catalog details', "");
            }
        } catch (Exception $e) {
            $this->NotifyUser('Error 23', $e->getMessage() . PHP_EOL);
        }
    }  //END EDIT CATALOG

    private function canUserAddToThisCatalog ($catalogId) {
            if ($catalogId <= 1) return true;
            $user = $this->session->get('User');
            $catalog = $this->getCatalogObject($catalogId);
            return $user->getUsername() == $catalog->getOwner();
    }

    public function getCatalogObject ($id) : Catalog {
        try
        {
            $stmt = $this->db->prepare("SELECT * FROM Catalogs WHERE catalogId = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if($file = $stmt->fetchObject('Catalog')) {
                return $file;
            }
            else {
                $this->NotifyUser("Catalog not found", "");
            }
        }
        catch(Exception $e) { $this->NotifyUser("Error 7", $e->getMessage()); }

    }

    public function deleteCatalog($id) : bool {
        $result = false;
        $this->session->remove('strHeader');
        $this->session->remove('strMessage');
        try
        {
            $stmt = $this->db->prepare("DELETE FROM Catalogs WHERE CatalogId = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount()==1) {
                $this->NotifyUser( "Catalog deleted", "");
                $result = true;
            } else {
                $this->NotifyUser( "Error 3", "");
                $result = false;
            }
        }
        catch (Exception $e) {
            $this->NotifyUser( "Error 4", $e->getMessage() . PHP_EOL);
        }
        return $result;
    }  //END DELETE CATALOG


    public function getCatalogs(int $catalogId)
    {
        $allCatalogs = null;
        try
        {
            $stmt = $this->db->prepare("SELECT * FROM Catalogs where parentId = :catalogId order by catalogName;");
            $stmt->bindParam(':catalogId', $catalogId, PDO::PARAM_INT);
            $stmt->execute();
            $allCatalogs = $stmt->fetchAll();
        }
        catch (Exception $e) { $this->NotifyUser("En feil oppstod", $e->getMessage()); }
        // bruk av data i video src
        //<source src="data:video/mp4;base64,{{ fil.kode }}">
        //foreach ($alleFiler as &$encode) {
        //    $encode['kode'] = base64_encode($encode['kode']);
        //}
        return $allCatalogs;
    }

    public function getCatalogsByOwner(string $owner) : array
    {
        $allCatalogs = null;
        try
        {
            $stmt = $this->db->prepare("SELECT * FROM Catalogs where owner = :owner order by catalogName;");
            $stmt->bindParam(':owner', $owner, PDO::PARAM_INT);
            $stmt->execute();
            $allCatalogs = $stmt->fetchAll();
        }
        catch (Exception $e) { $this->NotifyUser("En feil oppstod", $e->getMessage()); }
        // bruk av data i video src
        //<source src="data:video/mp4;base64,{{ fil.kode }}">
        //foreach ($alleFiler as &$encode) {
        //    $encode['kode'] = base64_encode($encode['kode']);
        //}
        return $allCatalogs;
    }

    /////////////////////////////////////////////////////////////////////////////
    /// FILE FUNCTIONS
    /// //////////////////////////////////////////////////////////////////////////

    public function save(string $owner) : int {

            $this->session->remove('strHeader');
            $this->session->remove('strMessage');

            $fileTags = $this->request->files->get('image');
            $file = $fileTags->getPathname();
            $name = $fileTags->getClientOriginalName();
            $type = $fileTags->getClientMimeType();
            $size = $fileTags->getSize();
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $tagsStr = filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_STRING);
            $catalogId = filter_input(INPUT_POST, 'catalogId', FILTER_SANITIZE_NUMBER_INT);
            if (!$this->canUserAddToThisCatalog($catalogId)) {
                $this->NotifyUser('User can\'t add to this catalog', '');
                return 0;
            }
            if ($this->request->request->has('access')) $access = 0;
            else $access = 1;
            // VIKTIG !  sjekk at vi jobber med riktig fil 
            if(is_uploaded_file($file) && $size != 0 && $size <= 5512000)
            {
                try
                {
                    $data = file_get_contents($file);
                    $stmt = $this->db->prepare("INSERT INTO `Files` (filename, title, description, catalogId, size, uploadedDate, type, access, data, owner)
                                                      VALUES (:filename, :title, :description, :catalogId, :size, now(), :type, :access, :data, :owner)");
                    $stmt->bindParam(':filename', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                    $stmt->bindParam(':catalogId', $catalogId, PDO::PARAM_INT);
                    $stmt->bindParam(':size', $size, PDO::PARAM_INT);
                    $stmt->bindParam(':type', $type, PDO::PARAM_STR);
                    $stmt->bindParam(':access', $access, PDO::PARAM_INT);
                    $stmt->bindParam(':data', $data, PDO::PARAM_LOB);
                    $stmt->bindParam(':owner', $owner, PDO::PARAM_STR);
                    $stmt->execute();
                    $id = intval($this->db->lastInsertId());
                    $this->addTags($tagsStr, $id);
                    $this->NotifyUser("Filen er lastet opp !", "");
                    return $id;
                }
                catch(Exception $e) { $this->NotifyUser("En feil oppstod", $e->getMessage()); return 0; }
            }
            else {
             //require_once ("hode.php");    
                    if ($size > 0) $this->NotifyUser("Filen er for stor !", "");
                    else $this->NotifyUser("Ingen filvedlegg", "");
                    return 0;
            }

        }  // END FILE SAVE



        // Viser oversikt over alle filer i db        
        public function getFilesOverview(int $catalogId)
        {
            $allFiles = null;
            try
            {
                $stmt = $this->db->prepare("SELECT * FROM Files where catalogId = :catalogId order by title;");
                $stmt->bindParam(':catalogId', $catalogId, PDO::PARAM_INT);
                $stmt->execute();
                $allFiles = $stmt->fetchAll();
            }
            catch (Exception $e) { $this->NotifyUser("En feil oppstod", $e->getMessage()); return; }
            // bruk av data i video src
            //<source src="data:video/mp4;base64,{{ fil.kode }}">
            //foreach ($alleFiler as &$encode) {
            //    $encode['kode'] = base64_encode($encode['kode']);
            //}
            return $allFiles;
        }     //END FILES OVERVIEW



        /*
            Viser en fil fra databasen
        */
        public function showFile($id)
        {
            try
            {
                $stmt = $this->db->prepare("SELECT type, impressions, filename, catalogId, data, access FROM Files WHERE fileId = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                if(!$item = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    throw new InvalidArgumentException('Invalid id: ' . $id);
                }
                else {
                    $filename = $item['filename'];
                    $type = $item['type'];
                    $data = $item['data'];
                    $access = $item['access'];
                    $impressions = $item['impressions'];
                    $catalogId = $item['catalogId'];

                    // check if public or not
                    if ($access == 0 or !$this->isCatalogAccessible($catalogId)) {
                        if ($this->session->has('User') && $this->session->has('loggedin')) {
                            $user = $this->session->get('User');
                            if ($this->session->get('loggedin') && $user->verifyUser($this->request)) {
                                Header( "Content-type: $type" );
                                Header("Content-Disposition: filename=\"$filename\"");
                                echo $data;
                                return true;
                            } else {$this->NotifyUser("Access denied, login to view file", ""); return false;}
                        } else {$this->NotifyUser("Access denied, login to view file", ""); return false;}
                    } elseif ($access == 1) {
                        $this->increaseImpression($id, $impressions);
                        Header( "Content-type: $type" );
                        Header("Content-Disposition: filename=\"$filename\"");
                        // Skriv bildet/filen til klienten
                        echo $data;
                        return true;
                    } else {$this->NotifyUser("Access denied, login to view file", ""); return false;}
                }
            }
            catch(Exception $e) {
                $this->NotifyUser("En feil oppstod", $e->getMessage());
                return false;
            }
        }



    private function increaseImpression($id, $impressions) {
        try
        {
            $impressions++;
            $stmt = $this->db->prepare("UPDATE Files SET impressions = :impr WHERE fileId = :id;");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':impr', $impressions, PDO::PARAM_INT);
            $stmt->execute();
            if(!$stmt->rowCount() == 1) {
                $this->NotifyUser("Error 12", '');
            }
        }
        catch(Exception $e) {
            $this->NotifyUser("Error 1", $e->getMessage());
        }
    }



    public function editFile($fileId)
    {
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $tagsStr = filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_STRING);
        $access = filter_input(INPUT_POST, 'access', FILTER_SANITIZE_NUMBER_INT);
        $catalogId = filter_input(INPUT_POST, 'catalogId', FILTER_SANITIZE_NUMBER_INT);
        if (!$this->canUserAddToThisCatalog($catalogId)) {
            $this->NotifyUser('User can\'t add to this catalog', '');
            return false;
        }
        if ($access == null) $access = 1;
        try {
            $sth = $this->db->prepare("update Files set title = :title, description = :description, catalogId = :catalogId, access = :access where fileId = :fileId");
            $sth->bindParam(':fileId', $fileId);
            $sth->bindParam(':title', $title);
            $sth->bindParam(':description', $description);
            $sth->bindParam(':catalogId', $catalogId);
            $sth->bindParam(':access', $access);
            $sth->execute();
            if ($sth->rowCount() == 1 | $this->addTags($tagsStr, $fileId)) {
                $this->NotifyUser('File details changed', '');
            } else {
                $this->NotifyUser('Failed to change file details', "");
            }
        } catch (Exception $e) {
            $this->NotifyUser('Error 2', $e->getMessage() . PHP_EOL);
        }
    }



    public function deleteFile($id) : bool {
        $result = false;
        $this->session->remove('strHeader');
        $this->session->remove('strMessage');
        try
        {
            $stmt = $this->db->prepare("DELETE FROM Files WHERE fileId = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount()==1) {
                $this->NotifyUser( "File deleted", "");
                $result = true;
            } else {
                $this->NotifyUser( "Error 3", "");
                $result = false;
            }
        }
        catch (Exception $e) {
            $this->NotifyUser( "Error 4", $e->getMessage() . PHP_EOL);
        }
        return $result;
    }



    public function getFileObject ($id) : File {
        try
        {
            $stmt = $this->db->prepare("SELECT Files.*, Catalogs.catalogName FROM `Files` INNER JOIN Catalogs ON Catalogs.catalogId = Files.catalogId WHERE Files.fileId = :id;");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if($file = $stmt->fetchObject('File')) {
                $tags = $this->getTags($id);
                $file->setTags($tags);
                return $file;
            }
            else {
                $this->NotifyUser("File not found", "");
                return new File();
            }
        }
        catch(Exception $e) { $this->NotifyUser("En feil oppstod", $e->getMessage());
        return new File(); }
    }



    public function searchFiles($searchQuery) {
        $searchQuery = str_replace('%','\\%', $searchQuery);
        $searchQuery = "%".$searchQuery."%";
        try
        {
            $stmt = $this->db->prepare("SELECT * FROM Files where title like :query or `description` like :query or `data` like :query;");
            $stmt->bindParam(':query', $searchQuery, PDO::PARAM_STR);
            $stmt->execute();
            $allFiles = $stmt->fetchAll();
        }
        catch (Exception $e) { $this->NotifyUser("En feil oppstod", $e->getMessage()); return;}
        return $allFiles;
    }




    public function searchFilesByTag($tag) {
        try
        {
            $stmt = $this->db->prepare("SELECT Files.*, FilesAndTags.tag FROM Files INNER JOIN `FilesAndTags` ON FilesAndTags.fileId = Files.fileId WHERE FilesAndTags.tag = :tag;");
            $stmt->bindParam(':tag',  $tag, PDO::PARAM_STR);
            $stmt->execute();
            $allFiles = $stmt->fetchAll();
        }
        catch (Exception $e) { $this->NotifyUser("En feil oppstod", $e->getMessage()); return;}
        return $allFiles;
    } //* END SEARCH BY TAG

    public function searchByMultipleTags($tagsStr) {
        $tagsStr = preg_replace("/[^\w\s\,]+/", "", $tagsStr);
        $tagsStr = preg_replace("/\s*,\s*/", ",", $tagsStr);
        $tagsStr = preg_replace("/\,*,\,*/", ",", $tagsStr);
        $tags = explode(',', $tagsStr);
        $tags = array_unique($tags);
        $tags = array_filter($tags);
        $files = array();
        foreach($tags as $tag) {
            $temp = $this->searchFilesByTag($tag);
            $files = array_merge($files, $temp);
        }
        return $files;
    } //* END SEARCH BY MULTIPLE TAGS






   public function getOnlyPublicFiles($files) : array {
            $publicFiles = array();
            $i = 0;
            foreach($files as $file) {
                if ($file['access'] == 1 && $this->isCatalogAccessible($file['catalogId'])) {
                    $publicFiles[$i] = $file;
                    $i++;
                }
            }
            return $publicFiles;
    }

}

?>