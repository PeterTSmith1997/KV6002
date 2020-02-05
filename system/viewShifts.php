<?php
session_start();
echo makeHeader('View shifts');
if ($_SESSION['type'] !== 'su'){
    echo "<p> not part of system</p>";
    echo makeFooter();

} else{
    echo "<div id=\"welcome\"> Welcome". $_SESSION['user']."/div>";
}