<?php
session_start();
require_once 'includes/functions.php';
echo makeHeader('View shifts');
if ($_SESSION['type'] !== 'su'){
    echo "<p> not part of system</p>";
    echo makeFooter();

} else{
    echo "<a href='chagePassword.php'> Password stuff</a>";
    echo "<div class='container'>
 <div  class='col-md3 offset-md-3' id='welcome'> Welcome ". $_SESSION['fName'] ."</div>
</div>";
    echo getShiftsAllocated(). makeBookingForm(). showErrors();
}
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
echo makeFooter();