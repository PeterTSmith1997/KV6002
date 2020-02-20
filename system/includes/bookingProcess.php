<?php /* This page should not be seen, it is purely for processing  */
/* Identify where the session data is located */
/* Starts a new session */
session_start();

/* Loads the functions file */
require_once 'functions.php';

unset($_SESSION['errors']);  // clear errors
$id = isset($_REQUEST{'id'})?$_REQUEST{'id'} : null;
list($input, $errors) = modifyShift();
if ($errors) {
    store_errors($errors);
    header("Location: " . $input['url']);
}
/* Else redirect the user */
else {

    header("Location: " . $input['url']);
}
