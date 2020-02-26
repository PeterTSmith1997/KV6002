<?php /* The user is never meant to see this page */
/* Identify where the session data is located */
/* Start the session */
session_start();

/* Assign a session to an array */
$_SESSION = array();

/* Destroy the session */
session_destroy();

/* Redirect user back to the page the user came from */
header("Location: https://tp.petersweb.me.uk/system/");