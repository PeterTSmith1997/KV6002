<?php

session_start();
require_once 'includes/functions.php';
echo makeHeader('Edit shifts');
if ($_SESSION['type'] !== 'su') {
    echo "<p> not part of system</p>";
    echo makeFooter();
} else{
      $id = isset($_REQUEST{'id'})?$_REQUEST{'id'} : null;

    if ($id == null){
        echo "Please go back"; // look at auto rediercton
    }
    else{
        echo makeEdidForm($id);
    }

}