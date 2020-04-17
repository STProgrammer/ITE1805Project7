<?php

class FileArchive {
        
        private $db;
        private $notification = array();
        private $request;
        private $session;
   
        function __construct($db, $request, $session) {
           
            $this->db = $db;
            $this->request = $request;
            $this->session = $session;
        } 
                
        private function NotifyUser($strHeader, $strMessage)
        {
            $this->session->set('strHeader', $strHeader);
            $this->session->set('strMessage', $strMessage);
        }

        private function addTags(string $tagsStr, int $id) {
            $tagsStr = preg_replace('{ [^ \w \s \,] }x', '', $tagsStr );
            $tags = explode(',', $tagsStr);
            $tags = array_unique($tags);
            $stmt = $this->db->prepare("INSERT IGNORE INTO Tags (tag) VALUES (:tag);");
            if(is_array($tags)){
                foreach ($tags as $tag) {
                    $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
                    $stmt->execute();
                }
            }
            $stmt = $this->db->prepare("INSERT INTO FilesAndTags (tag, fileId) VALUES (:tag, :id);");
            if(is_array($tags)){
                foreach ($tags as $tag) {
                    $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
        }

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


            if ($this->request->request->has('access')) $access = 0;
            else $access = 1;
            // VIKTIG !  sjekk at vi jobber med riktig fil 
            if(is_uploaded_file($file) && $size != 0 && $size <= 5512000)
            {
                    try
                    {
                        $data = file_get_contents($file);
                        $stmt = $this->db->prepare("INSERT INTO `Files` (filename, title, description, size, uploadedDate, type, access, data, owner)
					                                      VALUES (:filename, :title, :description, :size, now(), :type, :access, :data, :owner)");
                        $stmt->bindParam(':filename', $name, PDO::PARAM_STR);
                        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                        $stmt->bindParam(':size', $size, PDO::PARAM_INT);
                        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
                        $stmt->bindParam(':access', $access, PDO::PARAM_INT);
                        $stmt->bindParam(':data', $data, PDO::PARAM_LOB);
                        $stmt->bindParam(':owner', $owner, PDO::PARAM_STR);
                        $result = $stmt->execute();
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

        }        

        // Viser oversikt over alle filer i db        
        public function visOversikt()
        {
            $allFiles;
            $this->session->remove('strHeader');
            $this->session->remove('strMessage');
            try
            {
                $stmt = $this->db->query("SELECT fileId, filename, uploadedDate, type, data, access, title, size, impressions FROM Files order by filename;");
                $allFiles = $stmt->fetchAll();
            }
            catch (Exception $e) { $this->NotifyUser("En feil oppstod", $e->getMessage()); }
            // bruk av data i video src
            //<source src="data:video/mp4;base64,{{ fil.kode }}">
            //foreach ($alleFiler as &$encode) {
            //    $encode['kode'] = base64_encode($encode['kode']);
            //}

            return $allFiles;
        }             
  
        /*
            Viser en fil fra databasen
        */
        public function visFil($id)
        {
            try
            {
                $stmt = $this->db->prepare("SELECT mimetype,kode,filnavn,access FROM vedlegg_test WHERE id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                if(!$item = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    throw new InvalidArgumentException('Invalid id: ' . $id);
                }
                else {
                    $filnavn = $item['filnavn'];
                    $type = $item['mimetype'];
                    $data = $item['kode'];

                    // sett opp Mime type og Filnavn i header i henhold til verdier fra databasen
                    Header( "Content-type: $type" );
                    Header("Content-Disposition: filename=\"$filnavn\"");
                    // Skriv bildet/filen til klienten
                    echo $data;
                }
   
            }
            catch(Exception $e) { $this->NotifyUser("En feil oppstod", $e->getMessage()); }
        }
    public function getFileObject ($id) : File {
        try
        {
            $stmt = $this->db->prepare("SELECT * FROM Files WHERE fileId = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if($file = $stmt->fetchObject('File')) {
                return $file;
            }
            else {
                $this->NotifyUser("File not found", "");
            }
        }
        catch(Exception $e) { $this->NotifyUser("En feil oppstod", $e->getMessage()); }

    }
}
?>