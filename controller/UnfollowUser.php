<?php
    session_start();

    //if user not logged in, redirect to login
    if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== TRUE) {
        header("../view/location: login.php");
        exit;
    }

    require_once '../db.php';
    require_once '../models/Blogpost.php';
    require_once '../models/User.php';
    require_once '../Functions/convertToArray.php';
    require_once '../Functions/getParamsFromUrl.php';

    $profileUserId = getParamsFromUrl("user-id");
    $redirectTo = getParamsFromUrl("redirect");

    //instantiate user to unfollow
    $userToUnfollow = new User($db_connection);
    $userToUnfollow->getOne($profileUserId);
    
    //instantiate logged in user 
    $loggedInUser = new User($db_connection);
    $loggedInUser->getOne((int)$_SESSION["id"]);

    //if logged in user doesn't follow this user or tries to unfollow himself, exit
    if (!in_array($userToUnfollow->getId(),$loggedInUser->getFollowing()) || (string)$userToUnfollow->getId() === (string)$loggedInUser->getId()) {
        if ($redirectTo === "profile") {
            header("location: ../view/".$redirectTo.".php?user-id=".(string)$userToFollow->getId());
        } else {
        header("location: ".$redirectTo.".php");
        }
        exit;
    }

    //update followers of user that logged in user unfollows
    $userToUnfollow->removeUserFromList($loggedInUser->getId(),"followers");

    //update following of the logged in user
    $loggedInUser->removeUserFromList($userToUnfollow->getId(),"following");

    header("location: ../view/".$redirectTo.".php?user-id=".(string)$userToUnfollow->getId());
    exit();
?>