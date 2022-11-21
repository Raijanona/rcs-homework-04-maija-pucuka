<?php

require_once '../db.php';
require_once '../Functions/convertToArray.php';

class User {

    private $id;
    private $email;
    private $username;
    private $password;
    private $followers;
    private $following;
    private $blocked;
    private $profile_description;
    private $profile_picture;
    private $is_deleted;
    private $db_connection;

    public function __construct($db_connection) {
        $this->db_connection = $db_connection;
    }

    public function getOne($user_id) {
        $sql = "SELECT email,username,password,followers,following,blocked,profile_description,profile_picture FROM users WHERE id = ?";
        $stmt = $this->db_connection->stmt_init();
        $this->id = (int)$user_id;
        if ($stmt->prepare($sql)) {
            $stmt->bind_param("i", $param_userid);
            $param_userid = $this->id;
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($db_email,$db_username,$db_password,$db_followers,$db_following,$db_blocked,$db_profile_description,$db_profile_picture);
                    $stmt->fetch();

                    $this->email = $db_email;
                    $this->username = $db_username;
                    $this->password = $db_password;
                    $this->followers = convertToArray($db_followers);
                    $this->following = convertToArray($db_following);
                    $this->blocked = convertToArray($db_blocked);
                    $this->profile_description = $db_profile_description;
                    $this->profile_picture = $db_profile_picture;

                } else {
                    return FALSE;
                }
            }
        }
        $stmt->close();
    }


    public function registerNewUser($paramUsername,$paramEmail,$paramPassword) {
        
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $this->db_connection->stmt_init();
        $this->username = $paramUsername;

        if ($stmt->prepare($sql)) {
            $stmt->bind_param("s", $param_usernameVerif);

            $param_usernameVerif = $this->username;

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows() == 1) {
                    return FALSE;
                } else {
                    $paramUsername = $param_usernameVerif;
                }
            } else {
                echo "Something went wrong! Please try again";
            }

        } $stmt->close();

        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->db_connection->stmt_init();
        $this->email = $paramEmail;

        if ($stmt->prepare($sql)) {
            $stmt->bind_param("s", $param_emailVerif);

            $param_emailVerif = $this->email;

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $email_er = "Sorry this username is already taken! Please chose different one";
                    return $email_er;
                } else {
                    $paramEmail = $param_emailVerif;
                }
            } else {
                echo "Something went wrong! Please try again";
            }
        } $stmt->close();
        
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $this->db_connection->stmt_init();

        if ($stmt->prepare($sql)) {
            $stmt->bind_param("sss", $param_username, $param_email, $param_password);

            $param_username = $paramUsername;
            $param_email = $paramEmail;
            $param_password = password_hash($paramPassword, PASSWORD_DEFAULT);
    
            if ($stmt->execute()) {
                header("location: login.php");
            } else {
                echo "Something went wrong! Please try again";
            }
        }
        $stmt->close();
    
    }


    public function loginUser($usernameOrEmailParam,$paramPassword) {

        $sql = "SELECT id, username, email, password FROM users WHERE username= ? OR email = ?";
        $stmt = $this->db_connection->stmt_init();
        $this->username = $usernameOrEmailParam;
        $this->email = $usernameOrEmailParam;

        if ($stmt->prepare($sql)) {
            $stmt->bind_param("ss", $param_usernameOrEmail, $param_usernameOrEmail);

            $param_usernameOrEmail = $usernameOrEmailParam;
    
            if ($stmt->execute()) {
                $stmt->store_result();
    
                if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $username, $email, $hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($paramPassword,$hashed_password)) {
                            session_start();

                            $_SESSION = array();
        
                            $_SESSION["loggedin"] = TRUE;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
        
                            header("location: welcome.php");
                        } else {
                            return FALSE;
                        }
                    }
                } else {
                    return FALSE;
                }
    
    
           
            }
            $stmt->close();
        }
    }


    public function retrieveList ($loggedInUserId,$listToUpdate) {

        $sql = 'SELECT '.$listToUpdate.' FROM users WHERE id = ?';
        $stmt = $this->db_connection->stmt_init();
        $this->id = $loggedInUserId;

        if ($stmt->prepare($sql)) {
            $stmt->bind_param("i", $param_loggedInUserId);
            $param_loggedInUserId = $this->id;

            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($listRetrieved);
                    $stmt->fetch();

                    $listRetrieved = convertToArray($listRetrieved);
                    return $listRetrieved;
                }
            }
        }
        $stmt->close();
    }


    public function getAllFromList($paramRetrieved) {

        $sql = "SELECT * FROM users WHERE ";
        foreach ($paramRetrieved as $key => $value) {
            $sql = $sql . " id = " . $value;
            if ($key !== count($paramRetrieved) -1) {
                $sql = $sql . " OR";
            }
        }
        $stmt = $this->db_connection->stmt_init();
        if ($stmt->prepare($sql)) {
            if ($stmt->execute()) {
                $retrievedArray = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                return $retrievedArray;
            }
        }
        $stmt->close();

    }


    public function createNewUser($paramText, $param_user_id, $param_imageName) {

        $sql = "INSERT INTO users (profile_description, profile_picture) VALUES (?, ?) WHERE id = ?";
        $stmt = $this->db_connection->stmt_init();

        if($stmt->prepare($sql)) {
            $stmt->bind_param("ss", $paramDB_title, $paramDB_excerpt, $paramDB_text, $paramDB_user_id, $paramDB_imageName, $paramDB_gallery);
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


    public function isFollowingMe($user_id) {
        if (in_array($user_id, $this->followers)) {
            return TRUE;
        }
        return FALSE;
    }

    public function UserOwnsThisProfile($user_id) {
        return (int)$user_id === $this->id;
    }

    public function isOwner($user_id) {
        return (int)$user_id === $this->id;
    }

    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getFollowers() {
        return $this->followers;
    }

    public function getFollowing() {
        return $this->following;
    }

    public function getProfile_description() {
        return this->profile_description;
    }

    public function getProfile_picture() {
        return this->profile_picture;
    }

    public function getBlocked() {
        return $this->blocked;
    }

    public function addUserToList($userToAddToList, $listToUpdate) {
        $sql = 'UPDATE users SET '.$listToUpdate.' = ? WHERE id = ' . $this->id;
        $stmt = $this->db_connection->stmt_init();

        if ($stmt->prepare($sql)) {
            $stmt->bind_param("s",$paramNewList);
            $listNew = $this->$listToUpdate;
            $insertString = "[";

            foreach ($listNew as $value) {
                $insertString = $insertString. "'" .$value. "',";
            }

            $insertString = $insertString . "'" .$userToAddToList. "']";
            $paramNewList = $insertString;
            if ($stmt->execute()) {

            }

        }
        $stmt->close();
    }


    public function removeUserFromList($userToRemoveFromList, $listToUpdate) {
        $sql = 'UPDATE users SET '.$listToUpdate.' = ? WHERE id = ' . $this->id;
        $stmt = $this->db_connection->stmt_init();

        if ($stmt->prepare($sql)) {
            $stmt->bind_param("s",$paramNewList);
            $insertString = "[";

            foreach ($this->$listToUpdate as $value) {
                if ((string)$value !== (string)$userToRemoveFromList) {
                    $insertString = $insertString. "'" .$value. "',";
                }
                
            }

            $insertString = rtrim($insertString, ",");
            $insertString = $insertString."]";
            $paramNewList = $insertString;
            if ($stmt->execute()) {

            }

        }
        $stmt->close();
    }
    

}

?>