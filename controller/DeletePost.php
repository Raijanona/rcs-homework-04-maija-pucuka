<?php
session_start();

//if user not logged in, redirect to login
if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== TRUE) {
     header("../view/location: login.php");
     exit;
}

require_once "../db.php";
require_once '../Functions/getParamsFromUrl.php';
require_once '../models/Blogpost.php';
require_once '../models/User.php';

//get post-id from url param
$postId = getParamsFromUrl("post-id");

//create post and user instances
$user = new User ($db_connection);
$post = new Blogpost ($db_connection);

$post->getOne($postId);
$post_user_id = $post->getUserId();

$user->getOne($post_user_id);
$userOwnsProfile = $user->isOwner($_SESSION["id"]);

//if user owns profile delete post, if not redirect
if ($userOwnsProfile === TRUE) {
    $post->deletePost($postId);
    header("location: ../view/explore.php");
    exit;
} else if ($userOwnsProfile === FALSE) {
    header("location: ../view/explore.php");
    exit;
}

?>