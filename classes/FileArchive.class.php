<?php

class FileArchive {
        
        private $db;
        private $notification = array();
   
        function __construct($db) {
           
            $this->db = $db;
        } 
                
        private function NotifyUser($strHeader, $strMessage)
        {
            $this->notification['strHeader'] = $strHeader;
            $this->notification['strMessage'] = $strMessage;
            $_SESSION['strHeader'] = $strHeader;
            $_SESSION['strMessage'] = $strMessage;
        }

        function getNotification()
        {
            if (isset($_SESSION['strHeader']) && isset($_SESSION['strMessage'])) {
                $this->notification['strHeader'] = $_SESSION['strHeader'];
                $this->notification['strMessage'] = $_SESSION['strMessage'];
            }
            return $this->notification;
        }

        public function save() {

            if (isset($_SESSION['strHeader']) && isset($_SESSION['strMessage'])) {
                unset($_SESSION['strHeader']);
                unset($_SESSION['strMessage']);
            }
            $file = $_FILES[FILNAVN_TAG]['tmp_name'];
            $name = $_FILES[FILNAVN_TAG]['name'];
            $type = $_FILES[FILNAVN_TAG]['type'];
            $size = $_FILES[FILNAVN_TAG]['size'];
            if (isset($_POST['access'])) $access = 0;
            else $access = 1;
            // VIKTIG !  sjekk at vi jobber med riktig fil 
            if(is_uploaded_file($file) && $size != 0 && $size <= 5512000)
            {
                    try
                    {
                        $data = file_get_contents($file);
                        $stmt = $this->db->prepare("INSERT INTO `vedlegg_test` (size, dato, mimetype, filnavn, access, kode)
					                                      VALUES (:size, now(), :mimetype, :filnavn, :access, :kode)");
                        $stmt->bindParam(':size', $size, PDO::PARAM_INT);
                        $stmt->bindParam(':mimetype', $type, PDO::PARAM_STR);
                        $stmt->bindParam(':filnavn', $name, PDO::PARAM_STR);
                        $stmt->bindParam(':access', $access, PDO::PARAM_INT);
                        $stmt->bindParam(':kode', $data, PDO::PARAM_LOB);
                        $result = $stmt->execute();
                        $this->NotifyUser("Filen er lastet opp !", "");
                    }
                    catch(Exception $e) { $this->NotifyUser("En feil oppstod", $e->getMessage()); return; }
            }
            else {
             //require_once ("hode.php");    
                    if ($size > 0) $this->NotifyUser("Filen er for stor !", "");
                    else $this->NotifyUser("Ingen filvedlegg", "");
            }

        }        

        // Viser oversikt over alle filer i db        
        public function visOversikt()
        {
            unset($_SESSION['strHeader']);
            unset($_SESSION['strMessage']);
            try
            {
                $stmt = $this->db->query("SELECT id, filnavn, dato, mimetype, kode, access FROM vedlegg_test order by filnavn");
                $allFiles = $stmt->fetchAll();
            }
            catch(Exception $e) { $this->NotifyUser("En feil oppstod", $e-getMessage()); }
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
    public function getFileObject ($id) {
        try
        {
            $stmt = $this->db->prepare("SELECT * FROM vedlegg_test WHERE id = :id");
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