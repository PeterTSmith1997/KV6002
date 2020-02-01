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
    $page = $input['page'];
    /* IF they have come from the login page redirect to the home page */
    if ($page == "http://unn-w16018262.newnumyspace.co.uk/yr2/Assignment/login.php") {
        header("Location: http://unn-w16018262.newnumyspace.co.uk/yr2/Assignment/");
    }
    /* Redirect the user to the page they came from */
    else {
        header("Location: {$_SERVER['HTTP_REFERER']}");
    }
}
