<?php
require_once 'includes/functions.php';
session_start();
echo makeHeader("login");

?>
<h1>Welcome</h1>
<form id="login" action="includes/loginProcess.php" method="get">
    <div class="container">
        <label for="username"><b>Username</b></label>
        <input type="text" placeholder="Enter Username" name="username" required>

        <label for="password"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="password" required>
		
		<label for="UserType"><b>User Type </b></label>
		 <input type="radio" name="UserType" value="su"> service user<br>
         <input type="radio" name="UserType" value="staff"> staff<br>
        <button type="submit">Login</button>
    </div>

</form>
<?php
$errors = isset($_SESSION['errors'])? $_SESSION['errors']:null;
/* Checks if there are errors and it is of the expected type of array */
    if (!empty($errors) && is_array($errors)) {
        echo "<div class='error'>";
        /* Loop through the errors and display them on screen */
        foreach ($errors as $error) {
            echo "$error \n";
        }
    }
echo makeFooter();
?>