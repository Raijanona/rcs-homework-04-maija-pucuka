<?php
    session_start();

    //if user not logged in, redirect to login
    if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== TRUE) {
        header("location: login.php");
        exit;
    }

    require_once '../db.php';
    require_once '../models/Blogpost.php';
    require_once '../Functions/convertToArray.php';
    require_once '../Functions/validationFunctions.php';

    $paramTitle = $paramExcerpt = $paramText = $paramPicture = "";
    $title_err = $excerpt_err = $text_err = "";


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        //get POST values and chech if any of the fields are empty
        $paramTitle = trim($_POST["title"]);
        $paramExcerpt = trim($_POST["excerpt"]);
        $paramText = trim($_POST["text"]);


        $title_err = validateTitle($paramTitle);
        $excerpt_err = validateExcerpt($paramExcerpt);
        $text_err = validateText($paramText);

        //image function
        $tempname = $_FILES["uploadfile"]["tmp_name"];

        $filePath = tempnam('../images/', "");
        rename($filePath, $filePath .= '.png');
        $originalFileName = $_FILES["uploadfile"]["name"];
        unlink($filePath);
        $pathExploaded = explode("\\",$filePath);

        $filename = $pathExploaded[count($pathExploaded)-1];

        if (!move_uploaded_file($tempname, $filePath)) {
            $picture_err = "Please add a picture!";
        } else {
            $picture_err = FALSE;
        }

        //gallery function
        $galleryImages = $_FILES["gallery"];

        $galleryArray = reArrayFiles($galleryImages);

        $insertGalleryString = "[";

        foreach ($galleryArray as $key => $image) {

            $gallery_tempname = $image["tmp_name"];
            $gallery_filePath = tempnam('../images/', "");
            rename($gallery_filePath, $gallery_filePath .= '.png');
            unlink($gallery_filePath);
            $gallery_pathExploaded = explode("\\",$gallery_filePath);

            $gallery_filename = $gallery_pathExploaded[count($gallery_pathExploaded)-1];

            $insertGalleryString = $insertGalleryString. "'".$gallery_filename. "'";

            if ($key < count($galleryArray) -1 ) {
                $insertGalleryString = $insertGalleryString . ",";
            }

            if (!move_uploaded_file($gallery_tempname, $gallery_filePath)) {
                $gallery_err = "Please add a gallery!";
            } else {
                $gallery_err = FALSE;
            }
        }

        $insertGalleryString = $insertGalleryString . "]";

        $param_user_id = $_SESSION["id"];
        $param_imageName = $filename;
        $param_gallery = $insertGalleryString;

        //create new post instance
        $post = new Blogpost ($db_connection);

        //if all errors are empty, create new post
        if (empty($title_err) && empty($excerpt_err) && empty($text_err) && empty($picture_err) && empty($gallery_err)) {

            $post->createNewPost($paramTitle, $paramExcerpt, $paramText, $param_user_id, $param_imageName, $param_gallery);

        }


        
    }

    function reArrayFiles(&$file_post) {

        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }

        return $file_ary;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create new post</title>
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
                <h1>Fill out these fields to create a new post</h1>
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
                if (!empty($picture_err)) {
                    echo '<div class="alert alert-danger">' . $picture_err . '</div>';
                }
                if (!empty($gallery_err)) {
                    echo '<div class="alert alert-danger">' . $gallery_err . '</div>';
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Post title</label>
                        <input type="text" name="title" class="form-control" value=<?php echo $paramTitle; ?> >
                    </div>
                    <div class="form-group">
                        <label>Post excerpt</label>
                        <textarea style="min-height: 300px;" type="textarea" name="excerpt" class="form-control" >
                        <?php echo $paramExcerpt; ?>
                        </textarea>
                    </div>
                    <div class="form-group">
                        <label>Post thumbnail</label>
                        <input type="file" name="uploadfile" class="form-control" value=<?php echo $paramPicture; ?> >
                    </div>
                    <div class="form-group">
                        <label>Post text</label>
                        <textarea style="min-height: 200px;" type="textarea" name="text" class="form-control" >
                        <?php echo $paramText; ?>
                        </textarea>
                    </div>
                    <div class="form-group">
                        <label>Post gallery</label>
                        <input type="file" name="gallery[]" class="form-control" multiple value="">
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