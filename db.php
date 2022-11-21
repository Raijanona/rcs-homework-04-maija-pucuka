<?php

//define DB parameters
define("DB_SERVER", "localhost");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");
define("DB_NAME", "php_homework");

//create DB connection
$db_connection = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_NAME);

if ($db_connection == FALSE) {
    die("Something went wrong! Could not connect to database! Please try again!");
}
?>