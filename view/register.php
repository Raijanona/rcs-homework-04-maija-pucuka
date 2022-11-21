<?php
session_start();

//check if user is already logged in, if yes redirect to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === TRUE) {
    header("location: welcome.php");
    exit;
}

require_once "../db.php";
require_once "../Functions/validationFunctions.php";
require_once "../models/User.php";

$username = $email = $password = $password_confirm = "";
$username_err = $email_err = $password_err = $password_confirm_err = $username1_err = "";
$paramUsername = $paramPassword = $paramPasswordConfirm = $paramEmail = "";

//create new user instance
$user = new User ($db_connection); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //Validate usernama
    $paramUsername = trim($_POST["username"]);
    $username_err = validateUsername($paramUsername);

    //Validate passwords
    $paramPassword = trim($_POST["password"]);
    $password_err = validatePassword($paramPassword);

    $paramPasswordConfirm = trim($_POST["password_confirm"]);
    $password_confirm_err = validatePasswordConfirm($paramPasswordConfirm);

    if ($paramPassword !== $paramPasswordConfirm) {
        $password_confirm_err = "The passwords do not match! Please try again";
    } else {
        $password_confirm_err = FALSE;
    }
  
    //Validate email
    $paramEmail = trim($_POST["email"]);
    $email_err = validateEmail($paramEmail);

    //if all errors are empty, create new user
    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($password_confirm_err)) {
        if (($user->registerNewUser($paramUsername,$paramEmail,$paramPassword)) === FALSE) {
            $username_err = "Sorry this username is already taken! Please chose different one";
        } else {
            $email_err = "Sorry this email is already taken! Please chose different one";
        }
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/style/main.css">
</head>
<body>
    <div class="wrapper-guest">
        <div class="wrapper-top">
            <h1>Welcome to Raijanona's Travel Blog!</h1>
            <?php
            if (!empty($username_err)) {
                echo '<div class="alert alert-danger">' . $username_err . '</div>';
            }
            if (!empty($email_err)) {
                echo '<div class="alert alert-danger">' . $email_err . '</div>';
            }
            if (!empty($password_err)) {
                echo '<div class="alert alert-danger">' . $password_err . '</div>';
            }
            if (!empty($password_confirm_err)) {
                echo '<div class="alert alert-danger">' . $password_err . '</div>';
            }
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value=<?php echo $paramUsername; ?> >
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control" value=<?php echo $paramEmail; ?> >
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" value=<?php echo $paramPassword; ?> >
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirm" class="form-control" value=<?php echo $paramPasswordConfirm; ?> >
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Submit">
                </div>
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </form>
        </div>
    </div>
</body>
</html>