<?php


class File
{
    private $db;
    private $documentId;
    private $documentName;
    private $type;
    private $description;
    private $upLoadedDate;
    private $title;
    private $size;
    private $author;
    private $catalogueId;
    private $userId;
    private $impressions;
    private $access;
    private $kode;

    function __construct() { }

    public function showFile() {
        Header( "Content-type: $this->type" );
        Header("Content-Disposition: filename=\"$this->filnavn\"");
        // Skriv bildet/filen til klienten
        echo $this->kode;
        }

    public function getDb()
    {
        return $this->db;
    }


    public function setDb($db)
    {
        $this->db = $db;
    }

    public function getDocumentId()
    {
        return $this->documentId;
    }

    public function setDocumentId($documentId)
    {
        $this->documentId = $documentId;
    }

    public function getDocumentName()
    {
        return $this->documentName;
    }

    /**
     * @param mixed $documentName
     */
    public function setDocumentName($documentName)
    {
        $this->documentName = $documentName;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getUpLoadedDate()
    {
        return $this->upLoadedDate;
    }

    /**
     * @param mixed $upLoadedDate
     */
    public function setUpLoadedDate($upLoadedDate)
    {
        $this->upLoadedDate = $upLoadedDate;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getCatalogueId()
    {
        return $this->catalogueId;
    }

    /**
     * @param mixed $catalogueId
     */
    public function setCatalogueId($catalogueId)
    {
        $this->catalogueId = $catalogueId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @param mixed $impressions
     */
    public function setImpressions($impressions)
    {
        $this->impressions = $impressions;
    }

    /**
     * @return mixed
     */
    public function isAccessible()
    {
        return $this->access;
    }

    /**
     * @param mixed $access
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }




}