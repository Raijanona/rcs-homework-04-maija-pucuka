<?php
    $guest = TRUE;
    $userOwnsProfile = FALSE;
    $isFollowingThisUser = FALSE;

    //if user not logged in, redirect to login
    session_start();
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
        header("location: login.php");
        exit;
    } else if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === TRUE) {
        $guest = FALSE;
    }

    require_once '../db.php';
    require_once '../models/User.php';
    require_once '../models/Blogpost.php';
    require_once '../Functions/convertToArray.php';
    require_once '../Functions/getParamsFromUrl.php';

    //get user id param from url
    $userId = getParamsFromUrl("user-id");

    //create new user instance, get username, check if user owns the profile
    $user = new User ($db_connection);
    $user->getOne($userId);
    $profileUsername = $user->getUsername();
    $userOwnsProfile = $user->UserOwnsThisProfile($_SESSION["id"]);

    //create new post instance, get all posts from profile owner
    $post = new Blogpost ($db_connection);
    $allUserPosts = $post->getAllFromUser($userId);
    // $userOwnsProfile = $post->UserOwnsThisPost($_SESSION["id"]);

    //chech if logged in user is following profile owner
    $user_id = $_SESSION["id"];
    $isFollowingMe = $user->isFollowingMe($user_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/style/main.css">
    <link rel="stylesheet" href="../assets/style/fonts.css">
</head>
<body>
    <div class="wrapper wrapper-profile">
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
                    <a href="welcome.php">Go to dashboard</a>
                    <a href="explore.php">Explore</a>
                    <a href="followers.php?user-id=<?= $userId ?>">Followers</a>
                    <a href="following.php?user-id=<?= $userId ?>">Following</a>
                    <a href="createPost.php">Create a new post</a>
                    <a href="../controller/Logout.php">Sign Out</a>
                <?php } ?>
                </div>
        </div>
        <div class="main-wrap wrapper-profile">
            <h1 class="title"><?php echo ($userOwnsProfile === TRUE) ? "Welcome to your profile, " : "Viewing Profile of: " ?><?= $profileUsername ?> </h1>
            <h3><?php echo ($userOwnsProfile === TRUE) ? "Here are all your posts: " : "All posts: " ?></h3>
            <div class="wrapper-blogs">
                <?php foreach ($allUserPosts as $post) {?>
                    <a href="post.php?post-id=<?php echo $post["id"]; ?>">
                        <h2><?php echo $post["title"];?></h2>
                        <h2><?php echo $post["excerpt"];?></h2>
                    </a>
                <?php } ?>
            </div>
            <?php if (!$userOwnsProfile && !$guest) { ?>
                <?php if (!$isFollowingMe) { ?>
                    <a class="btn btn-normal" href="../controller/FollowUser.php?user-id=<?= $userId; ?>&redirect=profile">Follow</a>
                <?php } else { ?>
                    <a class="btn btn-normal" href="../controller/UnfollowUser.php?user-id=<?= $userId; ?>&redirect=profile">Unfollow</a>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <?php require_once "../components/footer.php" ?>
</body>
</html>