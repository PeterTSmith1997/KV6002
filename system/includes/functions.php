<?php
require_once 'config.php';
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 27/01/2020
 * Time: 19:17
 */
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
                $_SESSION['user'] = $input['username'];
                $_SESSION['fName'] = $input['name'];
                $_SESSION['loggedIn'] = true;
                $_SESSION['lastTime'] = time();

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
                    $_SESSION['user'] = $input['username'];
                    $_SESSION['fName'] = $input['name'];
                    $_SESSION['loggedIn'] = true;
                    $_SESSION['lastTime'] = time();
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
        <td scope='col'>edit</td>
</tr>
";

    $db = getConnection();

    $sql = "SELECT shifts.id as shift, ServiceU, Staff, StartDate, EndDate,
            StartTime, EndTime, FirstName, LastName  FROM `shifts` LEFT JOIN Staff ON (shifts.Staff = Staff.ID) WHERE ServiceU =:ServiceU";
    $stmt = $db->prepare($sql);
    $stmt->execute(array(':ServiceU' => $_SESSION['ID']));
    while ($recordObj = $stmt->fetchObject()){
     if ($recordObj) {
         if ($recordObj->Staff == null) {
             $id = $recordObj->shift;
             $unAllocated .= "<tr>
            <td scope='row'>  $recordObj->StartTime</td>
            <td>$recordObj->EndTime</td>
            <td>$recordObj->StartDate</td>
            <td><a href='editShift.php?id=$id'>edit</a> </td>
            </tr>";



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

}
function modifyShift(){

    $input = array();
    $errors = array();
    try {
        $db = getConnection();
        $input['id'] = filter_has_var(INPUT_GET, 'id') ? $_REQUEST['id'] : null;
        $input['date'] = filter_has_var(INPUT_GET, 'StartDate') ? $_REQUEST['StartDate'] : null;
        $input['Start'] = filter_has_var(INPUT_GET, 'Start') ? $_REQUEST['Start'] : null;
        $input['End'] = filter_has_var(INPUT_GET, 'end') ? $_REQUEST['end'] : null;
        $input['Notes'] = filter_has_var(INPUT_GET, 'Notes') ? $_REQUEST['Notes'] : null;
        $input['gender'] = $_REQUEST['gender'];

        var_dump($input);
        var_dump($_SESSION);




        if ($input['id'] == null) {
            $input['id'] = 1;
            $sql = "INSERT INTO `shifts`('ID','ServiceU', 'staff' 'StartDate', 'EndDate', 'StartTime', 'EndTime', 'Preferred gender') VALUES :id, :ServiceU, :staff, :StartDate,
          :EndDate, :StartTime, :EndTime, :Preferredgender";
            $stmt = $db->prepare($sql);
            if (!empty($input)) {
                $result = $stmt->execute(array(':id'=>$input['id'],':ServiceU' => $_SESSION['ID'], ':staff'=>null, ':StartDate' => $input['date'],
                    ':endDate' => $input['date'], ':StartTime' => $input['Start'], ':EndTime' => $input['End'],
                    ':Preferredgender' => $input['gender']));


            }
        } else {
            //update
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
        <form action='includes/bookingProcess.php' method="get">

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