<?php


class File
{
    private $db;
    private $fileId;
    private $filename;
    private $type;
    private $tags;
    private $description;
    private $uploadedDate;
    private $title;
    private $size;
    private $catalogId;
    private $catalogName;
    private $owner;
    private $impressions;
    private $access;
    private $data;

    function __construct() {
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return mixed
     */
    public function getCatalogName()
    {
        return $this->catalogName;
    }

    /**
     * @param mixed $catalogName
     */
    public function setCatalogName($catalogName)
    {
        $this->catalogName = $catalogName;
    }



    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }




    /**
     * @return mixed
     */
    public function getFileId()
    {
        return $this->fileId;
    }

    /**
     * @param mixed $fileId
     */
    public function setFileId($fileId)
    {
        $this->fileId = $fileId;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
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
    public function getUploadedDate()
    {
        return $this->uploadedDate;
    }

    /**
     * @param mixed $uploadedDate
     */
    public function setUploadedDate($uploadedDate)
    {
        $this->uploadedDate = $uploadedDate;
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
    public function getCatalogId()
    {
        return $this->catalogId;
    }

    /**
     * @param mixed $catalogId
     */
    public function setCatalogId($catalogId)
    {
        $this->catalogId = $catalogId;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
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
    public function getAccess()
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