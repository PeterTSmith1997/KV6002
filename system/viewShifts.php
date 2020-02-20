<?php
session_start();
require_once 'includes/functions.php';
echo makeHeader('View shifts');
if ($_SESSION['type'] !== 'su'){
    echo "<p> not part of system</p>";
    echo makeFooter();

} else{
    //nav bar needed here
    echo "<div class='container'>
 <div  class='col-md3 offset-md-3' id='welcome'> Welcome". $_SESSION['fName'] ."</div>
</div>";
    echo getShiftsAllocated();
    echo makeBookingForm();
    echo showErrors();
}

echo makeFooter();