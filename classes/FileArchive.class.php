<?php

class FileArchive {
        
        private $db; 
        private $twig;
        private $notification = array();
   
        function __construct($db, $twig) {
           
            $this->db = $db; 
            $this->twig = $twig;
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
            if (isset($_SERVER['strHeader']) && isset($_SERVER['strMessage'])) {
                $this->notification['strHeader'] = $_SESSION['strHeader'];
                $this->notification['strMessage'] = $_SESSION['strMessage'];
            }
            return $this->notification;
        }

        public function save() {
   
            $file = $_FILES[FILNAVN_TAG]['tmp_name'];
            $name = $_FILES[FILNAVN_TAG]['name'];
            $type = $_FILES[FILNAVN_TAG]['type'];
            $size = $_FILES[FILNAVN_TAG]['size'];
            // VIKTIG !  sjekk at vi jobber med riktig fil 
            if(is_uploaded_file($file) && $size != 0 && $size <= 5512000)
            {
                    try
                    {
                        $data = file_get_contents($file);
                        $stmt = $this->db->prepare("INSERT INTO `vedlegg_test` (size, dato, mimetype, filnavn, kode)
					                                      VALUES (:size, now(), :mimetype, :filnavn, :kode)");
                        $stmt->bindParam(':size', $size, PDO::PARAM_INT);
                        $stmt->bindParam(':mimetype', $type, PDO::PARAM_STR);
                        $stmt->bindParam(':filnavn', $name, PDO::PARAM_STR);
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

            try
            {
                $stmt = $this->db->query("SELECT id, filnavn, dato, mimetype, kode FROM vedlegg_test order by filnavn");
                $alleFiler = $stmt->fetchAll();

            }
            catch(Exception $e) { $this->NotifyUser("En feil oppstod", $e-getMessage()); }
            // bruk av data i video src
            //<source src="data:video/mp4;base64,{{ fil.kode }}">
            //foreach ($alleFiler as &$encode) {
            //    $encode['kode'] = base64_encode($encode['kode']);
            //}

            return $alleFiler;
        }             
  
        /*
            Viser en fil fra databasen
        */
        public function visFil($id)
        {
            try
            {

                $stmt = $this->db->prepare("SELECT mimetype,kode,filnavn FROM vedlegg_test WHERE id = :id");
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
}
?>