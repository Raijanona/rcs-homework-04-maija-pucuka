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

$usernameOrEmailParam = $paramPassword = "";
$usernameOrEmail_err = $password_err = "";

//create new user instance
$user = new User ($db_connection);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //get POST values and chech if any of the fields are empty
    $usernameOrEmailParam = trim($_POST["usernameOrEmail"]);
    $usernameOrEmail_err = validateUsernameOrEmail($usernameOrEmailParam);

    $paramPassword = trim($_POST["password"]);
    $password_err = validatePassword($paramPassword);

    //if all errors are empty, login user
    if (empty($usernameOrEmail_err) && empty($password_err)) {
        if(!$user->loginUser($usernameOrEmailParam,$paramPassword)) {
            $login_err = "Invalid username/email or password!";
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
            if (!empty($login_err)) {
                echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }
            if (!empty($usernameOrEmail_err)) {
                echo '<div class="alert alert-danger">' . $usernameOrEmail_err . '</div>';
            }
            if (!empty($password_err)) {
                echo '<div class="alert alert-danger">' . $password_err . '</div>';
            }
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Username or Email</label>
                    <input type="text" name="usernameOrEmail" class="form-control" value=<?php echo $usernameOrEmailParam; ?> >
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" value=<?php echo $paramPassword; ?> >
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Login">
                </div>
                <p>Don't have an account yet? <a href="register.php">Sign up now</a></p>
            </form>
        </div>
    </div>
</body>
</html>