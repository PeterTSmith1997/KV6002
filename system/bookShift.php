
<?php
session_start();
require_once 'includes/functions.php';
echo makeHeader('View shifts');
?>
<?php
if ($_SESSION['type'] !== 'su'){
    echo "<p> not part of system</p>";
    echo makeFooter();

} else{
    echo getNav();
    echo  makeBookingForm(). showErrors();
}

echo makeFooter();