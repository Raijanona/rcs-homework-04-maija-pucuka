<?php
    session_start();

    //if user not logged in, redirect to login
    if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== TRUE) {
        var_dump($_SESSION["loggedIn"]);
        die();
        header("location: ../view/login.php");
        exit;
    }

    require_once '../db.php';
    require_once '../models/User.php';
    require_once '../Functions/convertToArray.php';
    require_once '../Functions/getParamsFromUrl.php';

    //instantiate loggedInUser and blockedUser
    $loggedInUser = new User($db_connection);
    $blockedUser = new User($db_connection);

    $loggedInUser->getOne((int)$_SESSION["id"]);

    //get params from url
    $blockedUserId = getParamsFromUrl("user-id");
    $redirectTo = getParamsFromUrl("redirect");

    $blockedUser->getOne($blockedUserId);

    //if logged in user tries to block user that doesn't follow him or block himself then exit
    if ((!in_array($blockedUserId,$loggedInUser->getFollowers())) || ((string)$blockedUserId === (string)$loggedInUser->getId()) || (in_array($blockedUserId,$loggedInUser->getBlocked()))) {
        if($redirectTo == 'profile') {
            header("location: ../view/".$redirectTo.".php?user-id=".(string)$blockedUserId);
            exit;
        } else if($redirectTo == 'followers') {
            header("location: ../view/".$redirectTo.".php?user-id=".(string)$loggedInUser->getId());
            exit;
        } else {
            header("location: ".$redirectTo.".php");
            exit;
        }
    }

    /*UPDATE LOGGED IN USER*/


    //remove blocked user id from followers list of logged in user
    $loggedInUser->removeUserFromList($blockedUser->getId(),"followers");

    //add blocked user id to blocked list of logged in user
    $loggedInUser->addUserToList($blockedUser->getId(),"blocked");

    //remove blocked user id from following list of logged in user
    $loggedInUser->removeUserFromList($blockedUser->getId(),"following");


    /*UPDATE BLOCKED USER*/


    //remove logged in user id from following list of blocked user
    $blockedUser->removeUserFromList($loggedInUser->getId(),"following");

    //remove logged in user id from followers list of blocked user
    $blockedUser->removeUserFromList($loggedInUser->getId(),"followers");

    header("location: ../view/".$redirectTo.".php?user-id=".(string)$blockedUserId);
?>