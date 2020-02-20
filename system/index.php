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
echo showErrors() . makeFooter();
?>