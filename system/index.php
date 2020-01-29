<?php
require_once 'includes/functions.php';

echo makeHeader("login");

?>
<h1>Welcome</h1>
<form id="login" action="includes/login.php" method="get">
    <div class="container">
        <label for="uname"><b>Username</b></label>
        <input type="text" placeholder="Enter Username" name="username" required>

        <label for="psw"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="psw" required>
		
		<label for="UserType"><b>User Type </b></label>
		 <input type="radio" name="UserType" value="su"> service user<br>
         <input type="radio" name="gender" value="staff"> staff<br>
        <button type="submit">Login</button>
    </div>

</form>
<?php
echo makeFooter();
?>