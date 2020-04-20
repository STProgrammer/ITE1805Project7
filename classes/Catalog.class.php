<?php


class Catalog
{
    private $catalogId;
    private $parentId;
    private $catalogName;
    private $date;
    private $owner;
    private $access;

    public function getCatalogId()
    {
        return $this->catalogId;
    }

    public function setCatalogId($catalogId)
    {
        $this->catalogId = $catalogId;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    public function getCatalogName()
    {
        return $this->catalogName;
    }

    public function setCatalogName($catalogName)
    {
        $this->catalogName = $catalogName;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner($owner)
    {
        $this->userId = $owner;
    }

    public function getAccess()
    {
        return $this->access;
    }

    public function setAccess($access)
    {
        $this->access = $access;
    }


}