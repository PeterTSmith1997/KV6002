<?php
session_start();
require_once 'includes/functions.php';
echo makeHeader('View shifts');
if ($_SESSION['type'] !== 'su'){
    echo "<p> not part of system</p>";
    echo makeFooter();

} else{
    echo "<div id=\"welcome\"> Welcome". $_SESSION['name'] . "</div>";
    echo getShiftsAllocated();
}