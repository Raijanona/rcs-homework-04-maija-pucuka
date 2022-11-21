<?php
session_start();

//if user not logged in, redirect to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
    header("location: login.php");
    exit;
}

require_once '../db.php';
require_once '../models/User.php';
require_once '../models/Blogpost.php';
require_once '../Functions/validationFunctions.php';
require_once '../Functions/convertToArray.php';
require_once '../Functions/getParamsFromUrl.php';

$title_err = $excerpt_err = $text_err = "";

$post = new Blogpost ($db_connection);

if (!isset($_SESSION["post-id"]) && $_SESSION["post-id"] !== TRUE) {
    $postId = getParamsFromUrl("post-id");
    $_SESSION["post-id"] = $postId;
}

// $postId = getParamsFromUrl("post-id");
// $_SESSION["post-id"] = $postId;

//create new post instance, get all variables from post user wants to edit
$post->getOne($_SESSION["post-id"]);

$title = $post->getTitle();
$excerpt = $post->getExcerpt();
$text = $post->getText();
$post_user_id = $post->getUserId();
$date_published = $post->getDatePublished();

//create new user instance
$user = new User ($db_connection);

$user->getOne($post_user_id);

// if ($user->getOne($post_user_id) === FALSE) {
//     header("location: welcome.php");
//     exit;
// }

//check if user owns the post
// $userOwnsThisPost = $user->isOwner($_SESSION["id"]);
$userOwnsThisPost = $post->UserOwnsThisPost($_SESSION["id"]);

// if(!$userOwnsThisPost) {
//     var_dump($userOwnsThisPost);
//     die;
//     header("location: welcome.php");
//     exit;
// }

$postOwnerUsername = $user->getUsername();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //get POST values and chech if any of the fields are empty
    $paramTitle = trim($_POST["title"]);
    $paramExcerpt = trim($_POST["excerpt"]);
    $paramText = trim($_POST["text"]);


    $title_err = validateTitle($paramTitle);
    $excerpt_err = validateExcerpt($paramExcerpt);
    $text_err = validateText($paramText);

    //if all errors are empty, create new post
    if ($userOwnsThisPost) {
       if (empty($title_err) && empty($excerpt_err) && empty($text_err)) {
        $post->editPost($paramTitle, $paramExcerpt, $paramText);
        }
    
    } else {
        $post->getOne($postId);
    } 
} else {
    $postId = getParamsFromUrl("post-id");
    $_SESSION["post-id"] = $postId;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit post</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/style/main.css">
</head>
<body>
    <div class="wrapper">
        <div class="top sticky-top">
            <div class="plane">
                <img src="../assets\580b585b2edbce24c47b2d10.png">
            </div>
            <div class="nav-bar">
                <a href="profile.php?user-id=<?=$_SESSION["id"]?>">Profile</a>
                <a href="explore.php">Explore</a>
                <a href="followers.php?user-id=<?= $_SESSION["id"] ?>">Followers</a>
                <a href="following.php?user-id=<?= $_SESSION["id"] ?>">Following</a>
                <a href="../controller/Logout.php">Sign Out</a>
            </div>
        </div>
        <div class="wrapper-post">
            <div class="wrapper-post__text">
                <h1>Edit these fields to update content of your post</h1>
                <?php
                if (!empty($title_err)) {
                    echo '<div class="alert alert-danger">' . $title_err . '</div>';
                }
                if (!empty($excerpt_err)) {
                    echo '<div class="alert alert-danger">' . $excerpt_err . '</div>';
                }
                if (!empty($text_err)) {
                    echo '<div class="alert alert-danger">' . $text_err . '</div>';
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label>Post title</label>
                        <input type="text" name="title" class="form-control" value=<?php echo $title; ?> >
                    </div>
                    <div class="form-group">
                        <label>Post excerpt</label>
                        <input style="min-height: 100px" type="text" name="excerpt" class="form-control" value=<?php echo $excerpt; ?> >
                    </div>
                    <div class="form-group">
                        <label>Post text</label>
                        <textarea style="min-height: 400px;" type="textarea" name="text" class="form-control" >
                        <?php echo $text; ?>
                        </textarea>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Submit">
                    </div>
                </form>
                </div>
                <div class="wrapper-post__img">
                <img src="..\assets\images\travel_writing-shutterstock.jpg">
                </div>
        </div>
    </div>
    <?php require_once "../components/footer.php" ?>
</body>
</html>