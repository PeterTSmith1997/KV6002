<?php
session_start();
require_once 'includes/functions.php';
echo makeHeader('View shifts');
?>
<script type="application/javascript">
    function confirmationDelete(anchor)
    {
        var conf = confirm('Are you sure want to delete this record?');
        if(conf)
            window.location=anchor.attr("href");
    }
</script>
<?php
if ($_SESSION['type'] !== 'su'){
    echo "<p> not part of system</p>";
    echo makeFooter();

} else{
    echo "<a href='changePassword.php'> Password stuff</a>";
    echo "<a href='logout.php'>Logout</a>"; 
    echo "<div class='container'>
 <div  class='col-md3 offset-md-3' id='welcome'> Welcome ". $_SESSION['fName'] ."</div>
</div>";
    echo getShiftsAllocated(). makeBookingForm(). showErrors();
}

echo makeFooter();