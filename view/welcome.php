<?php
session_start();

//if user not logged in, redirect to explore
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
    header("location: explore.php");
    exit;
}

require_once "../db.php";
require_once '../models/User.php';
require_once '../models/Blogpost.php';

$loggedInUserId = (int)$_SESSION["id"];

//create new user instance, get all user following
$user = new User ($db_connection);

$followingRetrieved = $user->retrieveList($loggedInUserId,"following");

$post = new Blogpost ($db_connection);
$userFollowsSomeone = FALSE;

//if user is following someone, get all posts from following
if (count($followingRetrieved) > 0 && $followingRetrieved[0] !== '') {
    $userFollowsSomeone = TRUE;

    $allFollowingPosts = $post->getAllFromFollowers($followingRetrieved);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/style/main.css">
    <link rel="stylesheet" href="../assets/style/fonts.css">
</head>
<body>
    <div class="wrapper wrapper-welcome">
        <div class="top sticky-top">
            <div class="plane">
                <img src="../assets\580b585b2edbce24c47b2d10.png">
            </div>
            <div class="nav-bar">
                <a href="profile.php?user-id=<?=$_SESSION["id"]?>">Profile</a>
                <a href="explore.php">Explore</a>
                <a href="followers.php?user-id=<?= $_SESSION["id"] ?>">Followers</a>
                <a href="following.php?user-id=<?= $_SESSION["id"] ?>">Following</a>
                <a href="createPost.php">Create a new post</a>
                <a href="../controller/Logout.php">Sign Out</a>
            </div>
        </div>
        <div class="main-wrap">
                <?php if ($userFollowsSomeone) { ?>
                    <h1 class="title">Raijanona's travel blog</h1>
                    <h3>Here are all posts from users you are following:</h3>
                    <?php foreach ($allFollowingPosts as $post) {?>
                        <a href="post.php?post-id=<?= $post["id"] ?>">
                            <h2><?php echo $post["title"];?></h2>
                            <p><?php echo $post["excerpt"];?></p>
                        </a>
                    <?php } ?>
                <?php } else { ?>
                    <h3>You don't follow anyone yet. Check out <a href="explore.php">explore</a></h3>
                <?php } ?>
        </div>
    </div>
    <?php require_once "../components/footer.php" ?>
</body>
</html>