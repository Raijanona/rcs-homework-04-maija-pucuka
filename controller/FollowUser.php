<?php
    session_start();

    //if user not logged in, redirect to login
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
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

    //instantiate user to follow
    $userToFollow = new User($db_connection);
    $userToFollow->getOne($profileUserId);
    
    //instantiate logged in user 
    $loggedInUser = new User($db_connection);
    $loggedInUser->getOne((int)$_SESSION["id"]);

    //if logged in user already follows this user or tries to follow himself, exit
    if (in_array($userToFollow->getId(),$loggedInUser->getFollowing()) || (string)$userToFollow->getId() === (string)$loggedInUser->getId()) {
        if ($redirectTo === "profile") {
            header("location: ../view/".$redirectTo.".php?user-id=".(string)$userToFollow->getId());
        } else {
            header("location: ".$redirectTo.".php");
        }
        exit();
    }


    //update followers of user that gets new follower
    $userToFollow->addUserToList($loggedInUser->getId(),"followers");

    //update following of the logged in user
    $loggedInUser->addUserToList($userToFollow->getId(),"following");

    header("location: ../view/".$redirectTo.".php?user-id=".(string)$userToFollow->getId());

?>