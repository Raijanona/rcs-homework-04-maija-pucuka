<?php
    $userOwnsProfile = FALSE;
    $isFollowingThisUser = FALSE;
    $userOwnsThisPost = FALSE;
    
    session_start();

    if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== TRUE) {
        $id = "";
        $_SESSION["id"] = "";
    }

    require_once '../db.php';
    require_once '../models/Blogpost.php';
    require_once '../models/User.php';
    require_once '../Functions/convertToArray.php';
    require_once '../Functions/getParamsFromUrl.php';

    //get post param from url
    $postId = getParamsFromUrl("post-id");
    
    //create new post instance
    $post = new Blogpost ($db_connection);

    $post->getOne($postId);

    //check if user owns this post
    $userOwnsThisPost = $post->UserOwnsThisPost($_SESSION["id"]);
    
    //get all from post
    $title = $post->getTitle();
    $text = $post->getText();
    $post_user_id = $post->getUserId();
    $date_published = $post->getDatePublished();
    $postImageName = $post->getImageName();
    $galleryImages = $post->getGalleryImages();

    //create new user instance
    $user = new User ($db_connection);

    //get posw owners username
    $user->getOne($post_user_id);
    $postOwnerUsername = $user->getUsername();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viewing post</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/style/main.css">
    <link rel="stylesheet" href="../assets/style/fonts.css">
</head>
<body>
    <div class="wrapper">
        <div class="top sticky-top">
            <div class="plane">
                <img src="../assets\580b585b2edbce24c47b2d10.png">
            </div>
            <div class="nav-bar">
            <?php if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === TRUE)) { ?>
                    <a href="login.php">Sign In</a>
                    <a href="register.php">Create an account</a>
                    <a href="welcome.php">Home</a>
                <?php } else { ?>
                    <a href="profile.php?user-id=<?=$_SESSION["id"]?>">Profile</a>
                    <a href="explore.php">Explore</a>
                    <a href="followers.php?user-id=<?= $_SESSION["id"] ?>">Followers</a>
                    <a href="following.php?user-id=<?= $_SESSION["id"] ?>">Following</a>
                    <a href="createPost.php">Create a new post</a>
                    <a href="../controller/Logout.php">Sign Out</a> 
                <?php } ?>  
            </div>
        </div>
        <div class="main-wrap">
        <?php if ($userOwnsThisPost) { ?>
                <a class="btn btn-normal" href="editPost.php?post-id=<?=$postId ?>">Edit post</a>
            <?php } ?>
            <?php if ($userOwnsThisPost) { ?>
                <a class="btn btn-red" href="../controller/DeletePost.php?post-id=<?=$postId ?>">Delete post</a>
            <?php } ?>
            <h1 class="title"><?php echo $title?></h1>
            
            <h2><a href="profile.php?user-id=<?=$post_user_id ?>"><?php echo $postOwnerUsername ?></a></h2>
            
            <p><?= $date_published ?></p>
            <div class="thumbnail">
                <img src="../images/<?= $postImageName ?>">
            </div>
            <p><?= $text ?></p>
            <div class="image-wrapper">
                <?php foreach ($galleryImages as $key => $imageName) { ?>
                        <image src="../images/<?= $imageName ?>">
                <?php } ?>
            </div>
        </div>
    </div>
    <?php require_once "../components/footer.php" ?>
</body>
</html>