<?php
session_start();
$guest = TRUE;
$userOwnsProfile = FALSE;
$isFollowingThisUser = FALSE;

require_once '../db.php';
require_once '../models/Blogpost.php';

// create new post instance
$post = new Blogpost ($db_connection);

//get all posts
$allPosts = $post->getAll();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/style/main.css">
    <link rel="stylesheet" href="../assets/style/fonts.css">
</head>
<body>
<div class="wrapper wrapper-explore">
    <div class="top sticky-top">
        <div class="plane">
            <img src="../assets\580b585b2edbce24c47b2d10.png">
        </div>
        <div class="nav-bar">
            <?php if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === TRUE)) { ?>
                <a href="login.php">Sign In</a>
                <a href="register.php">Create an account</a>
            <?php } else { ?>
                <a href="welcome.php">Go to dashboard</a>
                <a href="profile.php?user-id=<?=$_SESSION["id"]?>">Profile</a>
                <a href="followers.php?user-id=<?= $_SESSION["id"] ?>">Followers</a>
                <a href="following.php?user-id=<?= $_SESSION["id"] ?>">Following</a>
                <a href="createPost.php">Create a new post</a>
                <a href="../controller/Logout.php">Sign Out</a>
            <?php } ?>
        </div>
    </div>
    <div class="main-wrap">
        <h1 class="title">Raijanona's travel blog</h1>
        <h3>Check these posts from Travel blog users: </h3>
        <div class="wrapper-blogs">
            <?php foreach ($allPosts as $post) {?>
                <div class="blog">
                    <a class="posts-explore" href="post.php?post-id=<?php echo $post["id"]; ?>">
                    <h2><?php echo $post["title"];?></h2>
                    <h2><?php echo $post["excerpt"];?></h2>    
                    </a>
                </div>
            <?php } ?>
        </div>   
    </div>
</div>
<?php require_once "../components/footer.php" ?>
</body>
</html>