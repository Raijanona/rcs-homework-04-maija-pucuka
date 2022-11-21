<?php

require_once '../db.php';
require_once '../Functions/convertToArray.php';

class Blogpost {

    private $id;
    private $title;
    private $excerpt;
    private $picture;
    private $text;
    private $date_published;
    private $is_deleted;
    private $image;
    private $gallery;
    private $post_owner_id;
    private $db_connection;

    public function __construct($db_connection) {
        $this->db_connection = $db_connection;
    }

    public function getOne($post_id) {
        $sql = "SELECT title,excerpt,picture,text,date_published,picture,gallery,user_id,is_deleted FROM blogposts WHERE id = ? AND NOT is_deleted";
        $stmt = $this->db_connection->stmt_init();
        $this->id = (int)$post_id;
        if ($stmt->prepare($sql)) {
            $stmt->bind_param("i", $param_id);
            $param_id = $this->id;
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($db_title,$db_excerpt,$db_picture,$db_text,$db_date_published,$db_image,$db_gallery,$db_post_owner_id,$db_is_deleted);
                    $stmt->fetch();

                    $this->title = $db_title;
                    $this->excerpt = $db_excerpt;
                    $this->picture = $db_picture;
                    $this->text = $db_text;
                    $this->date_published = $db_date_published;
                    $this->image = $db_image;
                    $this->gallery = convertToArray($db_gallery);
                    $this->post_owner_id = (int)$db_post_owner_id;
                    $this->is_deleted = $db_is_deleted;

                } else {
                    return FALSE;
                }  
                 
            }

        }$stmt->close();
    }


    public function getAll() {
        $sql = "SELECT * FROM blogposts WHERE NOT is_deleted";
        $stmt = $this->db_connection->stmt_init();
        if ($stmt->prepare($sql)) {
        
            if ($stmt->execute()) {
                $allPosts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                return $allPosts;  
            } else {
                return FALSE;
            }

        }$stmt->close();
    }


    public function getAllFromUser($userId) {
        $sql = "SELECT * FROM blogposts WHERE user_id = ? AND NOT is_deleted";
        $stmt = $this->db_connection->stmt_init();
        $this->post_owner_id = (int)$userId;
        if ($stmt->prepare($sql)) {
            $stmt->bind_param("i", $param_user_id);
            $param_user_id = $this->post_owner_id;
        
            if ($stmt->execute()) {
                $allUserPosts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                return $allUserPosts;  
            } else {
                return FALSE;
            }

        }$stmt->close();
    }


    public function getAllFromFollowers($followingRetrieved) {

        $sql = "SELECT * FROM blogposts WHERE NOT is_deleted";
        foreach ($followingRetrieved as $key => $value) {
            if ($key == 0) {
                $sql = $sql . " AND user_id = " . $value;
            } else {
                $sql = $sql . " OR user_id = " . $value;
            }
        }
        $stmt = $this->db_connection->stmt_init();
        if ($stmt->prepare($sql)) {
        
            if ($stmt->execute()) {
                    $allFollowersPosts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                    return $allFollowersPosts;
            } else {
                echo "error";
            }
        }
        $stmt->close();
    }


    public function editPost($titleEdited, $excerptEdited, $textEdited) {
            
        $sql = "UPDATE blogposts SET title = ?, excerpt = ?, text = ? WHERE id = ?";
        $stmt = $this->db_connection->stmt_init();
        
        if($stmt->prepare($sql)) {
            $stmt->bind_param("sssi", $param_editedTitle, $param_editedExcerpt, $param_editedText, $param_postId);
            $param_editedTitle = $titleEdited;
            $param_editedExcerpt = $excerptEdited;
            $param_editedText = $textEdited;
            $param_postId = $this->id;
            
            if($stmt->execute()) {
                $stmt->close();
                header("location: post.php?post-id=". $this->id);
                exit;
            } else {
                echo "Something went wrong! Please try again";
            }
                        
                    
                
        }
        
    }


    public function createNewPost($paramTitle, $paramExcerpt, $paramText, $param_user_id, $param_imageName, $param_gallery) {

        $sql = "INSERT INTO blogposts (title, excerpt, text, user_id, date_published, picture, gallery) VALUES (?, ?, ?, ?, NOW(), ?, ?)";
        $stmt = $this->db_connection->stmt_init();

        if($stmt->prepare($sql)) {
            $stmt->bind_param("sssiss", $paramDB_title, $paramDB_excerpt, $paramDB_text, $paramDB_user_id, $paramDB_imageName, $paramDB_gallery);
            $paramDB_title = $paramTitle;
            $paramDB_excerpt = $paramExcerpt;
            $paramDB_text = $paramText;
            $paramDB_user_id = $param_user_id;
            $paramDB_imageName = $param_imageName;
            $paramDB_gallery = $param_gallery;
            $this->id = $paramDB_user_id;


            if($stmt->execute()) {
                header("location: profile.php?user-id=".$this->id);
            }
        }
        $stmt->close();
    }
   

    public function deletePost($postId) {
        $sql = "UPDATE blogposts SET is_deleted = 1 WHERE id = ?";
        $stmt = $this->db_connection->stmt_init();
        $this->id = $postId;

        if ($stmt->prepare($sql)) {
            $stmt->bind_param("i",$param_deletedPostId);
            $param_deletedPostId = (int)$postId;

            if ($stmt->execute()) {
                header("location: ../view/explore.php");
                exit;
            }
        }
    }




    public function UserOwnsThisPost($user_id) {
        return (int)$user_id === $this->post_owner_id;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getExcerpt() {
        return $this->excerpt;
    }

    public function getPicture() {
        return $this->picture;
    }
    
    public function getText() {
        return $this->text;
    }

    public function getDatePublished() {
        $this->date_published;
        return $this->date_published;
    }

    public function getUserId() {
        return $this->post_owner_id;
    }

    public function getIsDeleted() {
        return $this->is_deleted;
    }

    public function getImageName() {
        return $this->image;
    }

    public function getGalleryImages() {
        return $this->gallery;
    }

}

?>