<?php
require_once 'config.php';
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 27/01/2020
 * Time: 19:17
 */
function getDateLocal(){return date("Y-m-d");}
function makeHeader($title){
    $header="<!DOCTYPE html>
<html lang=\"en\">
<head>
	<meta charset=\"utf-8\" />
  	<title>$title</title>
  		<meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">
    <link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css\" integrity=\"sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T\" crossorigin=\"anonymous\">
    <link rel=\"stylesheet\" href=\"css/main.css\"> 
</head>
<body>
<main>";

    return $header;
}
function makeFooter(){
    return "</main>
    </body>
    </html>";
}
function getConnection() {
    try {
        $user = user;
        $password = password;
        $db= dbName;
        $host = host;
        $connection = new PDO("mysql:host=$host;dbname=$db",
            "$user", "$password");
        $connection->setAttribute(PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION);
        return $connection;
    } catch (Exception $e) {
        /* We should log the error to a file so the developer can look at any logs. However, for now we won't */
        throw new Exception("Connection error ". $e->getMessage(), 0, $e);
    }
}

function validate_logon(){
    /** Create 2 empty arrays to store the input and any errors
     * Errors are kept as generic as possible in the interest of security*/
    $input = array();
    $errors = array();
    /** Request data from a form and check that there is data else assign a null value */
    $input['username'] = filter_has_var(INPUT_GET, 'username')?$_REQUEST['username'] : null;
    $input['password'] = filter_has_var(INPUT_GET, 'password')?$_REQUEST['password'] : null;
    $input['UserType'] = isset($_REQUEST{'UserType'})?$_REQUEST{'UserType'} : null;
    $errorPassword = false; // stops dipicate error
    /** Trim both inputs, assumes password does not allow spaces at the end */
    $input['username'] = trim($input['username']);
    $input['password'] = trim($input['password']);

    /** if statement to check that both fields have been completed */
    if ($input['username'] == null ^ $input['password'] == null ){
        $errors[] = "please  provide a user and password1";
        $errorPassword = true;
    }
    /** Checks to see if the username or password is empty, either both or each of them
     * and also checks if there is already error from the above if statement*/
    if ((empty($input['username']) ^ empty($input['password'])) && $errorPassword == false){
        $errors[] = "please  provide a user and password2";
    }

    $dbConn = getConnection();
    /** sql to select the password and first name from the database */
    if ($input['UserType'] == "su") {
        $sql = "SELECT ID, FirstName, LastName, Password
                       From ServiceUsers
                       WHERE EmailAddress = :email";
        $stmt = $dbConn->prepare($sql);
        $stmt->execute(array(':email' => $input['username']));

        $recordObj = $stmt->fetchObject();
        /** If statement to see if a row is returned */
        if ($recordObj) {
            $passwordHash = $recordObj->Password;
            /** Use password verify to make sure the password is correct and store data in the session */
            if (password_verify($input['password'], $passwordHash)) {
                $input['name'] = $recordObj->FirstName;
                $input['id'] = $recordObj->ID;
                $_SESSION['user'] = $input['username'];
                $_SESSION['fName'] = $input['name'];
                $_SESSION['loggedIn'] = true;
                $_SESSION['lastTime'] = time();
                $_SESSION['ID'] = $input['id'];
                $_SESSION['type'] = 'su';

            }
            else {
                $errors[] = "unknown user / password";
            }
        }

        /** If the password can not be verified */
        else {
            $errors[] = "unknown user / password";
        }
    }

    if ($input['UserType'] == "staff") {

        $sql = "SELECT ID, FirstName, LastName, Password
                       From Staff
                       WHERE EmailAddress = :username";

            $stmt = $dbConn->prepare($sql);
            $stmt->execute(array(':username' => $input['username']));

            $recordObj = $stmt->fetchObject();
            /** If statement to see if a row is returned */
            if ($recordObj) {
                $passwordHash = $recordObj->Password;
                /** Use password verify to make sure the password is correct and store data in the session */
                if (password_verify($input['password'], $passwordHash)) {
                    $input['name'] = $recordObj->FirstName;
                    $input['id'] = $recordObj->ID;
                    $_SESSION['user'] = $input['username'];
                    $_SESSION['fName'] = $input['name'];
                    $_SESSION['loggedIn'] = true;
                    $_SESSION['lastTime'] = time();
                    $_SESSION['ID'] = $input['id'];
                }
            }

            /** If the password can not be verified */
            else {
                $errors[] = "unknown user / password";
            }
        }
        else if ($input['UserType'] == null) {
            $errors[] = "Please select a user type";
        }

    /** Stores the page that they logged in from */
    $input['page'] = $_SERVER['HTTP_REFERER'];

    return array($input, $errors);
}
function store_errors($errors){
    $_SESSION['errors'] = $errors;
}
function getShiftsAllocated()
{
    $allocated = "<p>Your allocateted shits</p><table class='table table-hover'> 
        <tr>
        <td scope='col'>Start Time</td>
        <td scope='col'>End Time</td>
        <td scope='col'>Date</td>
        <td scope='col'>staff</td>
</tr>
";
    $unAllocated = "<p>Your un-allocateted shits</p><table class='table table-hover'> 

        <tr>

        <td scope='col'>Start Time</td>
        <td scope='col'>End Time</td>
        <td scope='col'>Date</td>
        <td scope='col' colspan='2'>edit</td>
</tr>
";

    $db = getConnection();

    $sql = "SELECT shifts.id as shift, ServiceU, Staff, StartDate, EndDate,
            StartTime, EndTime, FirstName, LastName  FROM `shifts` LEFT JOIN Staff ON (shifts.Staff = Staff.ID) WHERE ServiceU =:ServiceU AND StartDate > :today ";
    $stmt = $db->prepare($sql);
    $date=getDateLocal();
    $stmt->execute(array(':ServiceU' => $_SESSION['ID'], ':today'=>$date));
    while ($recordObj = $stmt->fetchObject()){
     if ($recordObj) {
         if ($recordObj->Staff == null) {
             $id = $recordObj->shift;
             $unAllocated .= "<tr>
            <td scope='row'>  $recordObj->StartTime</td>
            <td>$recordObj->EndTime</td>
            <td>$recordObj->StartDate</td>
            <td><a href='editShift.php?id=$id'>edit</a> </td>
            ";
             $unAllocated .= "<td><a onClick=\"javascript: return confirm('Please confirm deletion of this shift');\" href='delete.php?id=".$id."'>delete</a></td><tr>";



        } else {
            $staff = $recordObj-> FirstName . " " . $recordObj->LastName;
             $allocated .= "<tr>
            <td scope='row'>  $recordObj->StartTime</td>
            <td>$recordObj->EndTime</td>
            <td>$recordObj->StartDate</td>
            <td>$staff</td>
            </tr>
           ";
         }
     }
    }
    $tables = $allocated . "</table>". $unAllocated . "</table";
    return $tables;
}
function sendEmail($startTime, $date, $endTime){
    $name = $_SESSION['fName'];
    $message = <<<MESSAGE
HI $name\n
Your shift on $date between $startTime and $endTime has been added to the system. We will contact you shortly when it has been allocated to a member of staff.\n 
Thanks Evie 3000
MESSAGE;

    mail($_SESSION['user'],'Conformation of booking',$message);

}
function getID(){
    $db = getConnection();

    $sql = "SELECT MAX(ID) as ID from shifts";
    $result = $db->query($sql);

    return $result->fetchObject()->ID;
}
function modifyShift(){

    $input = array();
    $errors = array();
    try {
        $db = getConnection();

        $input['date'] = filter_has_var(INPUT_POST, 'StartDate') ? $_REQUEST['StartDate'] : null;
        $input['Start'] = filter_has_var(INPUT_POST, 'Start') ? $_REQUEST['Start'] : null;
        $input['End'] = filter_has_var(INPUT_POST, 'end') ? $_REQUEST['end'] : null;
        $input['gender'] = filter_has_var(INPUT_POST, 'gender') ? $_REQUEST['gender']:null;
        $date = strtotime($input['date']);
        $formattedDate = date("Y-m-d",$date);
        $today = getDateLocal();

        $errors[]=$today . "  ". $formattedDate;
        $input['url'] = 'https://tp.petersweb.me.uk/system/viewShifts.php';
        if ($formattedDate>$today){
            $errors[] = "Shift in past";
        }

        foreach ($input as $item){
            if ($item == null ){
                $errors[] = "Form item is null";
            }
        }
        //request id after null check as this can be null
        $input['id'] = filter_has_var(INPUT_POST, 'id') ? $_REQUEST['id'] : null;
        $input['staff'] = null;
        $input['Notes'] = filter_has_var(INPUT_POST, 'Notes') ? $_REQUEST['Notes'] : null;
        if (!$errors) {
            if ($input['id'] == null) {
                $input['id'] = getID() + 1;
                $sql = "INSERT INTO shifts(ID,ServiceU, staff, StartDate, EndDate, StartTime, EndTime, Preferredgender, notes) 
	                          VALUES (:id, :ServiceU, :staff, :StartDate, :EndDate, :StartTime, :EndTime, :Preferredgender, :notes)";

                $stmt = $db->prepare($sql);

                $stmt->execute(array(':id' => $input['id'],
                    ':ServiceU' => $_SESSION['ID'],
                    ':staff' => $input['staff'],
                    ':StartDate' => $input['date'],
                    ':EndDate' => $input['date'],
                    ':StartTime' => $input['Start'],
                    ':EndTime' => $input['End'],
                    ':Preferredgender' => $input['gender'],
                    ':notes' => $input['Notes']));
                sendEmail($input['Start'], $input['date'], $input['End']);
                $input['url'] = 'https://tp.petersweb.me.uk/system/viewShifts.php';

            } else {
                $updateSQL = "UPDATE shifts SET StartTime=:StartTime, EndTime=:EndTime,
                        Preferredgender=:Preferredgender, notes=:notes
                        WHERE id = :id";

                $stmt = $db->prepare($updateSQL);

                $stmt->execute(array(':id' => $input['id'],
                    ':StartTime' => $input['Start'],
                    ':EndTime' => $input['End'],
                    ':Preferredgender' => $input['gender'],
                    ':notes' => $input['Notes']));
                $input['url'] = 'https://tp.petersweb.me.uk/system/viewShifts.php';
            }

        }
    }
    catch (Exception $e){
        echo $e->getMessage();
    }

    return array($input, $errors);
}

function makeBookingForm(){
    $form = <<<FORM
     <div class="container">
        <form action='includes/bookingProcess.php' method="post">

            <label for="Start">Start</label>
            <input type="time" id="Start" name="Start">
            <label for="end">end</label>
            <input type="time" id="End" name="end">
            <label for="StartDate">Date</label>
            <input type="date" name="StartDate" id="StartDate">
            <label for="gender">Preferred gender</label>
            <fieldset id="gender">
                <input type="radio" name="gender" value="M"> Male<br>
                <input type="radio" name="gender" value="F"> Female<br>
                <input type="radio" name="gender" value="DM"> Dont mind<br>
            </fieldset>
            <label for="Notes">Any Other info</label>
            <textarea id="Notes" name="Notes"></textarea>
            <input type="submit" value="Submit">


        </form>
	
    </div>
FORM;

    return $form;
}
function makeEdidForm($id){
    $db = getConnection();
    $sql = "SELECT notes, StartDate, EndDate, StartTime, EndTime, Preferredgender, ServiceU
            FROM shifts
            WHERE ID = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute(array(':id' => $id));
    $recordObj = $stmt->fetchObject();
    var_dump($recordObj);
    $form = <<<FORM
    <p> edit  your shift on $recordObj->StartDate</p>
     <div class="container">
        <form action='includes/bookingProcess.php' method="post">
            <input type="hidden" id="id" name="id" value="$id">
            <label for="Start">Start</label>
            <input type="time" id="Start" name="Start" value="$recordObj->StartTime">
            <label for="end">end</label>
            <input type="time" id="End" name="end" value="$recordObj->EndTime">
            <label for="gender">Preferred gender</label>
            <fieldset id="gender">
FORM;
    if ($recordObj->Preferredgender=="M"){
        $form .= "
                <input type=\"radio\" name=\"gender\" value=\"M\" checked=\"checked\"> Male<br>
                <input type=\"radio\" name=\"gender\" value=\"F\"> Female<br>
                <input type=\"radio\" name=\"gender\" value=\"DM\"> Dont mind<br>";
    }else if ($recordObj->Preferredgender=="F"){
        $form .= "
                <input type=\"radio\" name=\"gender\" value=\"M\"> Male<br>
                <input type=\"radio\" name=\"gender\" value=\"F\" checked=\"checked\"> Female<br>
                <input type=\"radio\" name=\"gender\" value=\"DM\"> Dont mind<br>";
    }else{
        $form .= "
                <input type=\"radio\" name=\"gender\" value=\"M\" > Male<br>
                <input type=\"radio\" name=\"gender\" value=\"F\"> Female<br>
                <input type=\"radio\" name=\"gender\" value=\"DM\" checked=\"checked\"> Dont mind<br>";
    }
        $form.= <<<FORM2
    <label for="Notes">Any Other info</label>
            <textarea id="Notes" name="Notes">$recordObj->notes</textarea>
            <input type="submit" value="update">


        </form>
	
    </div>
FORM2;


    return $form;
}
function showErrors(){
     $string ="";
    $errors = isset($_SESSION['errors'])? $_SESSION['errors']:null;
    /* Checks if there are errors and it is of the expected type of array */
    if (!empty($errors) && is_array($errors)) {
        $string .= "<div class='p-3 mb-2 bg-danger text-white'>";
        /* Loop through the errors and display them on screen */
        foreach ($errors as $error) {
            $string .= "$error \n";
        }
    }
    return  $string;
}
function  passwordForm(){
    $form = <<<FORM
 <div class="container">
        <form action='includes/passwordProcess.php' method="post">
        
        <label for="passwordCurrent"><b>Current Password</b></label>
        <input type="password" placeholder="Enter Password" name="passwordCurrent" required>

        <label for="passwordNew"><b>New Password</b></label>
        <input type="password" placeholder="Enter Password" name="passwordNew" required>

        <label for="passwordConfirm"><b>Confirm Password</b></label>
        <input type="password" placeholder="Enter Password" name="passwordConfirm" required>
        
            <input type="submit" value="update">


        </form>
	
    </div>

FORM;
return $form;
}
function modifyPassword()
{

    $input = array();
    $errors = array();
    $input['passwordCurrent'] = filter_has_var(INPUT_GET, 'passwordCurrent') ? $_REQUEST['passwordCurrent'] : null;
    $input['passwordNew'] = filter_has_var(INPUT_GET, 'passwordNew') ? $_REQUEST['passwordNew'] : null;
    $input['passwordConfirm'] = filter_has_var(INPUT_GET, 'passwordConfirm') ? $_REQUEST['passwordConfirm'] : null;

    $dbConn = getConnection();
    /** sql to select the password*/
    if ($_SESSION['type'] == "su") {
        $sql = "SELECT Password
                       From ServiceUsers
                       WHERE ID = :id";
        $stmt = $dbConn->prepare($sql);
        $stmt->execute(array(':id' => $_SESSION['ID']));
        $dbConn = null;
        $recordObj = $stmt->fetchObject();
        /** If statement to see if a row is returned */
        if ($recordObj) {
            $passwordHash = $recordObj->Password;
            /** Use password verify to make sure the password is correct and store data in the session */
            if (password_verify($input['passwordCurrent'], $passwordHash)) {
                if ($input['passwordNew'] == $input['passwordConfirm']) {
                    $options = [
                        'cost'=>12,
                    ];
                    $input['newHash'] = password_hash($input['passwordNew'], PASSWORD_DEFAULT, $options);
                    $db = getConnection();
                    $updateSQL = "UPDATE ServiceUsers SET Password=:pw WHERE ID=id";

                    $stmt = $db->prepare($updateSQL);

                    $stmt->execute(array(':id'=>$_SESSION['ID'],
                                          ':pw'=>$input['newHash']));
                } else {

                }

            } else {
                $errors[] = "unknown user / password";
            }
        }
    }
    return array($input. $errors);
}
function deleteShift(){

    $input = array();
    $errors = array();
    
    $dbConn = getConnection();
    $input['id'] = isset($_REQUEST{'id'})?$_REQUEST{'id'} : null;
    //should probably check that the user is deleting one of their own shifts 
    $sql = "DELETE FROM shifts WHERE ID=:id";
    $stmt = $dbConn->prepare($sql)->execute(array('id'=>$input['id']));
    return array($input,$errors);
}

function getNav(){
    $nav = "<nav class=\"navbar sticky-top navbar-light bg-light\">
                <div class='collapse navbar-collapse' id='navbarNav'>
                    
</div>
        <ul class='navbar-nav'>
            <li class='nav-item'><a class='nav-link' href='changePassword.php'> Password stuff</a></li>
            <li class='nav-item'><a class='nav-link' href='logout.php'>Logout</a></li>
            </ul>
 </nav>\n ";
    return $nav;
}