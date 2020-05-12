<?php


class Comment
{
    private $commentId;
    private $comment;
    private $username;
    private $fileId;
    private $date;
    private $db;
    private $session;
    private $request;

    public function __construct(PDO $db, \Symfony\Component\HttpFoundation\Request $request,
                                \Symfony\Component\HttpFoundation\Session\Session $session)
    {
        $this->session = $session;
        $this->request = $request;
        $this->db = $db;
    }

    //Notify User, using FlashBag of session
    public function notifyUser($strHeader, $strMessage)
    {
        $this->session->getFlashBag()->add('header', $strHeader);
        $this->session->getFlashBag()->add('message', $strMessage);
    }

    //Add comment
    public function addComment(string $username, int $fileId) {

        $comment = $this->request->request->get('commenttext');

        try {
            $stmt = $this->db->prepare("INSERT INTO Comments (comment, fileId, username, date)
					                                      VALUES (:comment, :fileId, :username, now());");
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':fileId', $fileId, PDO::PARAM_INT);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $this->notifyUser("Comment added", "");
        }
        catch(Exception $e) { $this->notifyUser("Failed to add comment", ""); }
    }


    //Get comments made to a file
    public function getComments (int $fileId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Comments WHERE fileId = :fileId;");
            $stmt->bindParam(':fileId', $fileId, PDO::PARAM_INT);
            $stmt->execute();
            if($comments = $stmt->fetchAll()) {
                return $comments;
            }
        }
        catch(Exception $e) { $this->NotifyUser("Failed to load comments",""); }
    }


    // Get comment object
    public function getCommentObject (int $commentId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Comments WHERE commentId = :commentId;");
            $stmt->bindParam(':commentId', $commentId, PDO::PARAM_INT);
            $stmt->execute();
            if($comment = $stmt->fetchObject('Comment')) {
                return $comment;
            } else { $this->notifyUser("Something went wrong", ""); }
        }
        catch(Exception $e) { $this->notifyUser("Something went wrong", ""); }
    }


    //Check if user owns the comment or not
    public function checkOwner (string $username, int $commentId) {
        try {
            $stmt = $this->db->prepare("SELECT username FROM Comments WHERE commentId = :commentId;");
            $stmt->bindParam(':commentId', $commentId, PDO::PARAM_INT);
            $stmt->execute();
            if ($comment = $stmt->fetch()) {
                return $comment['username'] == $username;
            } else {
                $this->notifyUser("Something went wrong", "");
                return false;
            }
        } catch
        (Exception $e) { $this->notifyUser("Something went wrong", ""); }
        return false;
    }


    // Delete comment
    public function deleteComment(int $commentId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM Comments WHERE commentId = :commentId;");
            $stmt->bindParam(':commentId', $commentId, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount()==1) {
                $this->notifyUser("Comment deleted", "");
                return true;
            } else {
                $this->notifyUser( "Failed to delete comment", "");
                return false;
            }
        }
        catch(Exception $e) { $this->notifyUser("Failed to delete comment", ""); }

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