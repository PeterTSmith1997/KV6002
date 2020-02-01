<?php /* This page should not be seen, it is purely for processing  */
/* Identify where the session data is located */
/* Starts a new session */
session_start();

/* Loads the functions file */
require_once 'functions.php';

unset($_SESSION['errors']);  // clear errors

list($input, $errors) = validate_logon(); // validate login and create two arrays
/* IF there are any errors call the errors function and redirect user  to login */
if ($errors) {
    store_errors($errors);
    header("Location: https://tp.petersweb.me.uk/system/");
}
/* Else redirect the user */
else {
    header("Location: https://tp.petersweb.me.uk/system/viewShifts.php");
}
