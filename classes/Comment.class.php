<?php


class Comment
{
    private $commentId;
    private $comment;
    private $username;
    private $fileId;
    private $date;

    public function __construct()
    {

    }

    public static function addComment(PDO $db, $username, $fileId) {

        $comment = filter_input(INPUT_POST, 'commenttext', FILTER_SANITIZE_STRING);

        try {
            $stmt = $db->prepare("INSERT INTO Comments (comment, fileId, username, date)
					                                      VALUES (:comment, :fileId, :username, now());");
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':fileId', $fileId, PDO::PARAM_INT);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
        }
        catch(Exception $e) { self::NotifyUser("En feil oppstod", $e->getMessage()); }
    }

    public static function getComments (PDO $db, $fileId) {
        try {
            $stmt = $db->prepare("SELECT * FROM Comments WHERE fileId = :fileId;");
            $stmt->bindParam(':fileId', $fileId, PDO::PARAM_INT);
            $stmt->execute();
            if($comments = $stmt->fetchAll()) {
                return $comments;
            }
        }
        catch(Exception $e) { self::NotifyUser("Fail to submit comment", $e->getMessage()); }
    }

    public static function getCommentObject (PDO $db, $commentId) {
        try {
            $stmt = $db->prepare("SELECT * FROM Comments WHERE commentId = :commentId;");
            $stmt->bindParam(':commentId', $commentId, PDO::PARAM_INT);
            $stmt->execute();
            if($comment = $stmt->fetchObject('Comment')) {
                return $comment;
            } else { self::NotifyUser("En feil oppstod", ""); }
        }
        catch(Exception $e) { self::NotifyUser("Fail to submit comment", $e->getMessage()); }
    }

    public static function checkOwner (PDO $db, $username, $commentId) {
        try {
            $stmt = $db->prepare("SELECT username FROM Comments WHERE commentId = :commentId;");
            $stmt->bindParam(':commentId', $commentId, PDO::PARAM_INT);
            $stmt->execute();
            if ($comment = $stmt->fetch()) {
                return $comment['username'] == $username;
            } else {
                self::NotifyUser("En feil oppstod", "");
                return false;
            }
        } catch
            (Exception $e) { self::NotifyUser("Fail to submit comment", $e->getMessage()); }
            return false;
    }


    public static function deleteComment(PDO $db, $commentId) {
        try {
            $stmt = $db->prepare("DELETE FROM Comments WHERE commentId = :commentId;");
            $stmt->bindParam(':commentId', $commentId, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount()==1) {
                return true;
            } else {
                self::NotifyUser( "Error 3", "");
                return false;
            }
        }
        catch(Exception $e) { self::NotifyUser("En feil oppstod", $e->getMessage()); }

    }

    public static function NotifyUser($strHeader, $strMessage)
    {
        exit();
    }

    /**
     * @return mixed
     */
    public function getCommentId()
    {
        return $this->commentId;
    }

    /**
     * @param mixed $commentId
     */
    public function setCommentId($commentId)
    {
        $this->commentId = $commentId;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
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
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }




}

?>