<?php
session_start();

//if user not logged in, redirect to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
     header("location: explore.php");
     exit;
}

require_once "../db.php";
require_once '../models/User.php';

$loggedInUserId = (int)$_SESSION["id"];

//create new user instance, get all user followers
$user = new User ($db_connection);
$followersRetrieved = $user->retrieveList($loggedInUserId,"followers");

$userHasFollower = FALSE;

//if user has a follower, get all followers from DB
if (count($followersRetrieved) > 0 && $followersRetrieved[0] !== '') {
    $userHasFollower = TRUE;

    $followersArray = $user->getAllFromList($followersRetrieved);

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Followers</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/style/main.css">
    <link rel="stylesheet" href="../assets/style/fonts.css">
</head>
<body>
<div class="wrapper wrapper-followers">
    <div class="top sticky-top">
        <div class="plane">
            <img src="../assets\580b585b2edbce24c47b2d10.png">
        </div>
        <div class="nav-bar">
            <a href="profile.php?user-id=<?=$_SESSION["id"]?>">Profile</a>
            <a href="explore.php">Explore</a>
            <a href="welcome.php">Go to dashboard</a>
            <a href="following.php?user-id=<?= $_SESSION["id"] ?>">Following</a>
            <a href="createPost.php">Create a new post</a>
            <a href="../controller/Logout.php">Sign Out</a>
        </div>
    </div>
    <div class="main-wrap">
        <?php if ($userHasFollower) { ?>
            <?php foreach ($followersArray as $follower) {?>
                <a href="profile.php?user-id=<?= $follower["id"] ?>">
                    <h1><?php echo $follower["username"];?></h1>
                    <a class="btn btn-red" href="../controller/BlockUser.php?user-id=<?= $follower["id"] ?>&redirect=followers">Block user</a>
                </a>
            <?php } ?>
        <?php } else { ?>
            <h3>You don't have any followers yet</a></h3>
        <?php } ?>
    </div>
</div>
<?php require_once "../components/footer.php" ?>
</body>
</html>