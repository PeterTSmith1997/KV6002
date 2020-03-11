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
    echo getNav();

    echo  getShiftsAllocated(). showErrors();
}

echo makeFooter();      
