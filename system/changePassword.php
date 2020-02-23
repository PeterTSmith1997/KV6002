<?php

session_start();
require_once 'includes/functions.php';
echo makeHeader('Edit shifts');
if ($_SESSION['type'] !== 'su') {
    echo "<p> not part of system</p>";
    echo makeFooter();
} else{
    echo passwordForm().makeFooter();
}