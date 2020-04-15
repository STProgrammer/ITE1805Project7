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
    private $catalogId;
    private $userId;
    private $impressions;
    private $access;
    private $data;

    function __construct() { }

    public function showFile() {
        Header( "Content-type: $this->type" );
        Header("Content-Disposition: filename=\"$this->filnavn\"");
        // Skriv bildet/filen til klienten
        echo $this->kode;
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

    public function setDocumentName($documentName)
    {
        $this->documentName = $documentName;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getDescription()
    {
        return $this->description;
    }


    public function setDescription($description)
    {
        $this->description = $description;
    }


    public function getUpLoadedDate()
    {
        return $this->upLoadedDate;
    }


    public function setUpLoadedDate($upLoadedDate)
    {
        $this->upLoadedDate = $upLoadedDate;
    }

    public function getTitle()
    {
        return $this->title;
    }


    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function getCatalogId()
    {
        return $this->catalogId;
    }

    public function setCatalogId($catalogId)
    {
        $this->catalogId = $catalogId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getImpressions()
    {
        return $this->impressions;
    }

    public function setImpressions($impressions)
    {
        $this->impressions = $impressions;
    }

    public function getAccess()
    {
        return $this->access;
    }

    public function setAccess($access)
    {
        $this->access = $access;
    }

    public function getKode()
    {
        return $this->kode;
    }

    public function setKode($kode)
    {
        $this->kode = $kode;
    }


}