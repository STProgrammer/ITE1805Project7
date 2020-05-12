<?php

class FileArchive {

    private $db;
    private $request;
    private $session;

    function __construct(PDO $db, \Symfony\Component\HttpFoundation\Request $request,
                         \Symfony\Component\HttpFoundation\Session\Session $session) {

        $this->db = $db;
        $this->request = $request;
        $this->session = $session;
    }

    //* NOTIFY USER

    private function notifyUser(String $strHeader, String $strMessage)
    {
        $this->session->getFlashBag()->add('header', $strHeader);
        $this->session->getFlashBag()->add('message', $strMessage);
    }

    //* END NOTIFY USER

    /////////////////////////////////////////////////////////////////////////////
    /// PRIVATE FUNCTIONS
    /// //////////////////////////////////////////////////////////////////////////

    //Fix tags string
    private function fixTagsString(string $tagsStr) : string {
        $tagsStr = preg_replace("/[^\w\s\,]+/", "", $tagsStr);  //Only alphanumerics and spaces
        $tagsStr = preg_replace("/\s*,\s*/", ",", $tagsStr);  //Get rid of spaces before or after comma ,
        $tagsStr = preg_replace("/\,*,\,*/", ",", $tagsStr); //Get rid of stuff like ",," or ",,,,"
        return $tagsStr;
    }

    //Add tags
    private function addTags(string $tagsStr, int $fileId) : bool {
        $tagsStr = $this->fixTagsString($tagsStr);
        $tags = explode(',', $tagsStr);
        $tags = array_unique($tags);
        $tags = array_filter($tags);
        try {
            // First remove all tags of the file
            $stmt = $this->db->prepare("DELETE FROM FilesAndTags where fileId = :fileId;");
            $stmt->bindParam(':fileId', $fileId, PDO::PARAM_INT);
            $stmt->execute();
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
                $this->removeUnusedTags();
            }
        } catch (Exception $e) { $this->notifyUser("Failed to add tags", $e->getMessage()); return false;}
        return true;
    } //* END ADD TAGS


    private function removeUnusedTags() {
        try {
            $stmt = $this->db->query("delete Tags from Tags left join FilesAndTags on Tags.tag = FilesAndTags.tag where FilesAndTags.tag IS NULL;");
            $stmt->execute();
        } catch (Exception $e) {
            $this->notifyUser("Something went wrong", $e->getMessage());
        }
    }


    //Get tags
    private function getTags(int $id) : String {
        try {
            $stmt = $this->db->prepare("SELECT tag FROM FilesAndTags WHERE fileId = :id;");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $tags = implode(", ", $tags);
            return $tags;
        }
        catch (Exception $e) { return "Failed to loads tags";}
    } //end get tags

    //Increase impression when file is viewed or downloaded
    private function increaseImpression($id, $impressions) {
        try
        {
            $impressions++;
            $stmt = $this->db->prepare("UPDATE Files SET impressions = :impr WHERE fileId = :id;");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':impr', $impressions, PDO::PARAM_INT);
            $stmt->execute();
            if(!$stmt->rowCount() == 1) {
                $this->notifyUser("Something went wrong", '');
            }
        }
        catch(Exception $e) {
            $this->notifyUser("Something went wrong", $e->getMessage());
        }
    }



    /////////////////////////////////////////////////////////////////////////////
    /// CATALOG FUNCTIONS
    /// //////////////////////////////////////////////////////////////////////////

    //Get catalog path string e.g. "main / catalog1 / subcatalog"
    public function getCatalogPath($catalogId) : String {
        if ($catalogId <= 1 ) {
            return "Main";
        }
        try {
            $stmt = $this->db->prepare("SELECT catalogName, parentId FROM Catalogs WHERE catalogId = :catalogId;");
            $stmt->bindParam(':catalogId', $catalogId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();
            $parentId = $row['parentId'];
            $catalogPath = $this->getCatalogPath($parentId) . " / " . $row['catalogName'];
            return $catalogPath;
        }
        catch (Exception $e) { return "Failed to show catalog path" . $e->getMessage(); }
    }


    //Check if catalog is public or not, if a catalog is not public, all it's son are not public
    private function isCatalogAccessible(int $id) : bool {
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
                $this->notifyUser("Catalog not found", "");
                return false;
            }
        }
        catch(Exception $e) { $this->notifyUser("Something went wrong", $e->getMessage()); }
    } //* END IsCatalogAccessible


    //Can user show the catalog?
    public function showCatalog($id) {
        if (!$this->isCatalogAccessible($id)) {
            if (($user = $this->session->get('User')) && $this->session->get('loggedin')
            && $user->verifyUser($this->request)) {
                return true;
            } else {$this->notifyUser("Access denied, login to view catalog", ""); return false;}
        } else {
            return true;
        }
    }  //* END showCatalog


    // Add catalog
    public function addCatalog(string $owner) : int {

        $catalogName = $this->request->request->get('catalogName');
        if ($this->request->request->has('access')) $access = 0;
        else $access = 1;
        $parentId = $this->request->request->get('catalogId');
        if (!$this->canUserAddToThisCatalog($parentId)) {
            $this->notifyUser('User can\'t add to this catalog', '');
            return 0;
        }
        try
        {
            $stmt = $this->db->prepare("INSERT INTO Catalogs (catalogName, parentId, date, owner, access) VALUES (:catalogName, :parentid, curdate(), :owner, :access);");
            $stmt->bindParam(':catalogName', $catalogName, PDO::PARAM_STR);
            $stmt->bindParam(':parentid', $parentId, PDO::PARAM_INT);
            $stmt->bindParam(':owner', $owner, PDO::PARAM_STR);
            $stmt->bindParam(':access', $access, PDO::PARAM_INT);
            $stmt->execute();
            $id = intval($this->db->lastInsertId());
            $this->notifyUser("New catalog added !", "");
            return $id;
        }
        catch(Exception $e) { $this->notifyUser("Failed to add catalog", $e->getMessage()); return 0; }
    }  //* END Add catalog


    // Edit catalog
    public function editCatalog($catalogId)
    {
        $catalogName = $this->request->request->get('catalogName');
        $access = $this->request->request->get('access');
        $parentId = $this->request->request->get('catalogId');
        if ($access == null) $access = 1;
        if (!$this->canUserAddToThisCatalog($parentId)) {
            $this->notifyUser('User can\'t add to this catalog', '');
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
                $this->notifyUser('Catalog details changed', '');
            } else {
                $this->notifyUser('Failed to change catalog details', "");
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to change catalog details", $e->getMessage() . PHP_EOL);
        }
    }  //END EDIT CATALOG


    //Check if user can add to this catalog
    private function canUserAddToThisCatalog ($catalogId) {
        if ($catalogId <= 1) return true;
        $user = $this->session->get('User');
        $catalog = $this->getCatalogObject($catalogId);
        return $user->getUsername() == $catalog->getOwner();
    }

    // Get catalog object
    public function getCatalogObject ($id) : Catalog {
        try
        {
            $stmt = $this->db->prepare("SELECT * FROM Catalogs WHERE catalogId = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if($catalog = $stmt->fetchObject('Catalog')) {
                return $catalog;
            }
            else {
                $this->notifyUser("Catalog not found", "");
                return new Catalog();
            }
        }
        catch(Exception $e) { $this->notifyUser("Something went wrong", $e->getMessage());
            return new Catalog();}

    }


    //Delete catalog
    public function deleteCatalog($id) : bool {
        $result = false;
        try
        {
            $stmt = $this->db->prepare("DELETE FROM Catalogs WHERE CatalogId = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount()==1) {
                $this->notifyUser( "Catalog deleted", "");
                $result = true;
            } else {
                $this->notifyUser( "Failed to delete catalog", "");
                $result = false;
            }
            $this->removeUnusedTags();
        }
        catch (Exception $e) {
            $this->notifyUser( "Failed to delete catalog", $e->getMessage() . PHP_EOL);
        }
        return $result;
    }  //END DELETE CATALOG


    // Get Catalogs


    //Get catalogs by owner (get all catalogs of the user)
    public function getCatalogsByOwner(string $owner) : array
    {
        $allCatalogs = null;
        try
        {
            $stmt = $this->db->prepare("SELECT * FROM Catalogs where owner = :owner order by catalogName;");
            $stmt->bindParam(':owner', $owner, PDO::PARAM_STR);
            $stmt->execute();
            $allCatalogs = $stmt->fetchAll();
        }
        catch (Exception $e) { $this->notifyUser("Something went wrong", $e->getMessage()); }
        return $allCatalogs;
    }

    /////////////////////////////////////////////////////////////////////////////
    /// FILE FUNCTIONS
    /// //////////////////////////////////////////////////////////////////////////

    //SAVE FILE ON DATABASE (taken from Knut Collin)
    public function saveFile(string $owner) : int {
        $fileTags = $this->request->files->get('image');
        $file = $fileTags->getPathname();
        $name = $fileTags->getClientOriginalName();
        $type = $fileTags->getClientMimeType();
        $size = $fileTags->getSize();
        $title = $this->request->request->get('title');
        $description = $this->request->request->get('description');
        $tagsStr = $this->request->request->get('tags');
        $catalogId = $this->request->request->get('catalogId');
        if (!$this->canUserAddToThisCatalog($catalogId)) {
            $this->notifyUser('User can\'t add to this catalog', '');
            return 0;
        }
        if ($this->request->request->has('access')) $access = 0;
        else $access = 1;
        // VIKTIG !  sjekk at vi jobber med riktig fil
        if(is_uploaded_file($file) && $size != 0 && $size <= 5512000 && strlen($name) <= 60)
        {
            try
            {
                $data = file_get_contents($file);
                $stmt = $this->db->prepare("INSERT INTO `Files` (filename, title, description, catalogId, size, uploadedDate, type, access, data, owner)
                                                      VALUES (:filename, :title, :description, :catalogId, :size, curdate(), :type, :access, :data, :owner)");
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
    //            if (exif_imagetype($file)) {$this->saveThumbnail($data, $id);}
                $this->addTags($tagsStr, $id);
                $this->notifyUser("File uploaded", "");
                return $id;
            }
            catch(Exception $e) { $this->notifyUser("Failed to upload file", $e->getMessage()); return 0; }
        }
        else {
            //require_once ("hode.php");
            if ($size > 5512000) $this->notifyUser("Failed to upload: File size is too big !", "");
            elseif(strlen($name) > 60) $this->notifyUser("Failed to upload: Filename is too long", "");
            else $this->notifyUser("No file found", "");
            return 0;
        }

    }  // END FILE SAVE




    //GET OVERVIEW OF FILES AND CATALOGS
    public function getOverview(int $catalogId, int $offset, int $nrOfElementsPerPage)
    {
        $allElements = null;
        try
        {
            $stmt = $this->db->prepare("SELECT * FROM Elements where Catalog = :catalogId order by isFile, Title LIMIT :offset, :nrOfElementsPerPage;");
            $stmt->bindParam(':catalogId', $catalogId, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindParam(':nrOfElementsPerPage', $nrOfElementsPerPage, PDO::PARAM_INT);
            $stmt->execute();
            $allElements = $stmt->fetchAll();
        }
        catch (Exception $e) { $this->notifyUser("Failed to load files and catalogs", $e->getMessage()); return; }
        return $allElements;
    }     //END FILES AND CATALOGS OVERVIEW



    //Show a file from the database
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
                            $this->increaseImpression($id, $impressions);
                            Header( "Content-type: $type" );
                            Header("Content-Disposition: filename=\"$filename\"");
                            echo $data;
                            return true;
                        } else {$this->notifyUser("Access denied, login to view file", ""); return false;}
                    } else {$this->notifyUser("Access denied, login to view file", ""); return false;}
                } elseif ($access == 1) {
                    $this->increaseImpression($id, $impressions);
                    Header( "Content-type: $type" );
                    Header("Content-Disposition: filename=\"$filename\"");
                    // Skriv bildet/filen til klienten
                    echo $data;
                    return true;
                } else {$this->notifyUser("Access denied, login to view file", ""); return false;}
            }
        }
        catch(Exception $e) {
            $this->notifyUser("Something went wrong", $e->getMessage());
            return false;
        }
    }  //END SHOW FILE


    //Show thumbnail, delvis modifisert fra https://stackoverflow.com/questions/21709098/resizing-and-then-displaying-blob-element-from-database
    public function showThumbnail($id) {
        try {
            $stmt = $this->db->prepare("SELECT type, impressions, filename, data FROM Files WHERE fileId = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if (!$item = $stmt->fetch(PDO::FETCH_ASSOC)) {
                throw new InvalidArgumentException('Invalid id: ' . $id);
            } else {
                $filename = $item['filename'];
                $type = $item['type'];
                $data = $item['data'];
                $image = imagecreatefromstring($data);
                $image = imagescale($image, 110, 100);
                header('Content-Type: image/jpeg');
                imagejpeg($image);
                imagedestroy($image);
                return true;
            }
        }
        catch(Exception $e) {
            $this->notifyUser("Failed to show thumbnail", $e->getMessage());
            return false;
        }
    }


    //EDIT file
    public function editFile($fileId)
    {
        $title = $this->request->request->get('title', FILTER_SANITIZE_STRING);
        $description = $this->request->request->get('description', FILTER_SANITIZE_STRING);
        $tagsStr = $this->request->request->get('tags', FILTER_SANITIZE_STRING);
        $access = $this->request->request->get('access');
        $catalogId = $this->request->request->get('catalogId');
        if (!$this->canUserAddToThisCatalog($catalogId)) {
            $this->notifyUser('User can\'t add to this catalog', '');
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
                $this->notifyUser('File details changed', '');
            } else {
                $this->notifyUser('Failed to change file details', "");
            }
            $this->removeUnusedTags();
        } catch (Exception $e) {
            $this->notifyUser('Failed to change file details', $e->getMessage() . PHP_EOL);
        }
    }  // END EDIT FILE


    // Delete file
    public function deleteFile($id) : bool {
        try
        {
            $stmt = $this->db->prepare("DELETE FROM Files WHERE fileId = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount()==1) {
                $this->notifyUser( "File deleted", "");
                $result = true;
            } else {
                $this->notifyUser( "Failed to delete file", "");
                $result = false;
            }
            $this->removeUnusedTags();
        }
        catch (Exception $e) {
            $this->notifyUser( "Failed to delete file", $e->getMessage() . PHP_EOL);
        }
        return $result;
    }  //END DELETE FILE



    // Get file object
    public function getFileObject ($id) : File {
        try
        {
            $stmt = $this->db->prepare("SELECT Files.*, Catalogs.catalogName FROM `Files` INNER JOIN Catalogs ON Catalogs.catalogId = Files.catalogId WHERE Files.fileId = :id;");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if($file = $stmt->fetchObject('File')) {
                $tags = $this->getTags($id);
                $file->setTags($tags);
                $catalogPath = $this->GetCatalogPath($file->getCatalogId());
                $file->setCatalogName($catalogPath);
                return $file;
            }
            else {
                $this->notifyUser("File not found", "");
                return new File();
            }
        }
        catch(Exception $e) { $this->notifyUser("Something went wrong", $e->getMessage());
            return new File(); }
    } //END GET FILE OBJECT



    // Search files
    public function searchFiles($searchQuery) {
        $searchQuery = str_replace('%','\\%', $searchQuery);
        $searchQuery = "%".$searchQuery."%";
        $fromDate = $this->request->query->get('from-date');
        $toDate = $this->request->query->get('to-date');

        try
        {
            if (DateTime::createFromFormat('Y-m-d', $toDate) || DateTime::createFromFormat('Y-m-d', $fromDate)) {
                //To if koder slik at søk fungerer også når den ene er tom
                if ($fromDate == "") {$fromDate = "2000-1-1";}
                if ($toDate == "") {$toDate = date("Y-m-d");}
                $stmt = $this->db->prepare("SELECT * FROM Elements where isFile = 1 and (Date > :fromdate and Date <= :todate) and (Title like :query or Description like :query or (`Data` like :query and (`Type` REGEXP 'text'))) order by Date;");
                $stmt->bindParam(':query', $searchQuery, PDO::PARAM_STR);
                $stmt->bindParam(':fromdate', $fromDate, PDO::PARAM_STR);
                $stmt->bindParam(':todate', $toDate, PDO::PARAM_STR);
            }
            else {
                $stmt = $this->db->prepare("SELECT * FROM Elements where isFile = 1 and (Title like :query or Description like :query or (`Data` like :query and (`Type` REGEXP 'text')));");
                $stmt->bindParam(':query', $searchQuery, PDO::PARAM_STR);
            }
            $stmt->execute();
            $allFiles = $stmt->fetchAll();
        }
        catch (Exception $e) { $this->notifyUser("Something went wrong when searching", $e->getMessage()); return;}
        return $allFiles;
    } //End search files



    // Search files bu tag
    public function searchFilesByTag($tag) {
        try
        {
            $stmt = $this->db->prepare("SELECT * FROM FilesWithTagsView WHERE tag = :tag;");
            $stmt->bindParam(':tag',  $tag, PDO::PARAM_STR);
            $stmt->execute();
            $allFiles = $stmt->fetchAll();
        }
        catch (Exception $e) { $this->notifyUser("En feil oppstod", $e->getMessage()); return;}
        return $allFiles;
    } //* END SEARCH BY TAG



    // Search files by tags with OR condition (search files containing "tag1" or "tag2" or "tag3" or ... )
    public function searchByTagsWithOrCondition($tagsStr) {
        if ($tagsStr == "") {return array();}
        $tagsStr = $this->fixTagsString($tagsStr);
        $tagsArray = explode(",", $tagsStr);
        $placeHolder = ":" . str_replace(",", ", :", $tagsStr);
        $placeHolderArray = explode(", ", $placeHolder);
        $query ="SELECT * FROM FilesWithTagsView WHERE tag IN (". $placeHolder . ") GROUP BY id;";
        $size = sizeof($tagsArray);
        try
        {
            $stmt = $this->db->prepare($query);
            for ($i = 0; $i < $size; $i++) {
                $stmt->bindParam($placeHolderArray[$i], $tagsArray[$i], PDO::PARAM_STR);
            }
            $stmt->execute();
            $filesByTags = $stmt->fetchAll();
        }
        catch (Exception $e) { $this->notifyUser("Something went wrong", $e->getMessage()); return;}
        return $filesByTags;
    } //* END SEARCH BY MULTIPLE TAGS WITH OR CONDITION


    // Search files by tags with AND condition (search files containing both "tag1" and "tag2" and "tag3" and ...)
    public function searchByTagsWithAndCondition($tagsStr) {
        if ($tagsStr == "") {return array();}
        $tagsStr = $this->fixTagsString($tagsStr);
        $tagsArray = explode(",", $tagsStr);
        $placeHolder = ":" . str_replace(",", ", :", $tagsStr);
        $placeHolderArray = explode(", ", $placeHolder);
        $size = sizeof($tagsArray);
        $query ="SELECT * FROM (SELECT * FROM FilesWithTagsView WHERE tag IN (".$placeHolder.")) as TagsTemp group by id having count(id)>=".$size.";";
        $size = sizeof($tagsArray);
        try
        {
            $stmt = $this->db->prepare($query);
            for ($i = 0; $i < $size; $i++) {
                $stmt->bindParam($placeHolderArray[$i], $tagsArray[$i], PDO::PARAM_STR);
            }
            $stmt->execute();
            $filesByTags = $stmt->fetchAll();
        }
        catch (Exception $e) { $this->notifyUser("Something went wrong", $e->getMessage()); return;}
        return $filesByTags;
    } //* END SEARCH BY MULTIPLE TAGS WITH AND CONDITION




    //Get total number of pages (used in pagination, depending on nr of elements)
    public function totalNrOfPages($nrOfElementsPerPage, $catalogId) : int {
        $totalPages = 1;
        try
        {
            $stmt = $this->db->prepare("SELECT count(*) FROM Elements WHERE Catalog = :catalogId;");
            $stmt->bindParam(':catalogId', $catalogId, PDO::PARAM_INT);
            $stmt->execute();
            $totalRows = $stmt->fetch();
            $totalPages = ($totalRows['0'] == 0) ? 1 : ceil($totalRows['0'] / $nrOfElementsPerPage);
        }
        catch (Exception $e) { $this->notifyUser("Something went wrong", $e->getMessage()); }
        return $totalPages;
    }


    //Get an array of only public files, used in searching so results only show public files if user is not logged inn
    public function getOnlyPublicFiles($files) : array {
        $publicFiles = array();
        $i = 0;
        foreach($files as $file) {
            if ($file['Access'] == 1 && $this->isCatalogAccessible($file['Catalog'])) {
                $publicFiles[$i] = $file;
                $i++;
            }
        }
        return $publicFiles;
    }

}

?>