<?php

function validateTitle($paramTitle) {
    if(empty($paramTitle)) {
        $title_err = "Please enter title of your blog!";
    } else if (strlen($paramTitle) > 200) {
        $title_err = "Your title is too long!";
    } else {
        $title_err = FALSE;
    }
    return $title_err;
}

function validateExcerpt($paramExcerpt) {
    if(empty($paramExcerpt)) {
        $excerpt_err = "Please enter excerpt of your blog!";
    } else if (strlen($paramExcerpt) > 200) {
        $excerpt_err = "Your excerpt is too long!";
    } else {
        $excerpt_err =  FALSE;
    }
    return $excerpt_err;
}

function validateText($paramText) {
    if(empty($paramText)) {
        $text_err = "Please enter text of your blog!";
    } else {
        $text_err = FALSE;
    }
    return $text_err;
}

function validateUsername($paramUsername) {
    if(empty($paramUsername)) {
        $username_err = "Please enter your username!";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/',$paramUsername)) {
        $username_err = "Your username contains characters that are not allowed! You can only use numbers, latin letters and underscore";
    } else {
        $username_err = FALSE;
    }
    return $username_err;
}

function validatePassword($paramPassword) {
    if(empty($paramPassword)) {
        $password_err = "Please enter your password!";
    } else if (strlen($paramPassword) < 6) {
        $password_err = "Your password is too short! It should be at least 6 characters long";
    } else {
        $password_err = FALSE;
    }
    return $password_err;
}

function validatePasswordConfirm($paramPasswordConfirm) {
    if(empty($paramPasswordConfirm)) {
        $password_confirm_err = "Please repeat your password!";
    } else {
        $password_confirm_err = FALSE;
    }
    return $password_confirm_err;
}

function validateEmail($paramEmail) {
    if(empty($paramEmail)) {
        $email_err = "Please enter your email!";
    } else if (!filter_var($paramEmail, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format! Please try again!";
    } else {
        $email_err = FALSE;
    }
    return $email_err;
}

function validateUsernameOrEmail($usernameOrEmailParam) {
    if(empty($usernameOrEmailParam)) {
        $usernameOrEmail_err = "Please enter your email or username!";
    } else {
        $usernameOrEmail_err = FALSE;
    }
    return $usernameOrEmail_err;
}

function validatePicture($pictureParam) {
    if(empty($pictureParam)) {
        $picture_err = "Please add a picture!";
    } else {
        $picture_err = FALSE;
    }
    return $picture_err;
}



?>