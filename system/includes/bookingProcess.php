<?php /* This page should not be seen, it is purely for processing  */
/* Identify where the session data is located */
/* Starts a new session */
session_start();

/* Loads the functions file */
require_once 'functions.php';

unset($_SESSION['errors']);  // clear errors
$id = isset($_REQUEST{'id'})?$_REQUEST{'id'} : null;
list($input, $errors) = modifyShift(); 
if ($id != null ) {
    store_errors($errors);
    header("Location: https://tp.petersweb.me.uk/system/");
}
/* Else redirect the user */
else {
    header("Location: https://tp.petersweb.me.uk/system/viewShifts.php");
}
