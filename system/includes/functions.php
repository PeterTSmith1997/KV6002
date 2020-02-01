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
    $input['UserType'] = filter_has_var(INPUT_GET, 'UserType')? $_REQUEST['UserType'] : null;
    $errorPassword = false; // stops dipicate error
    $_SESSION['user'] = $input['username'];
    $_SESSION['pass'] = $input['password'];
    $_SESSION['ut'] = $input['UserType'];
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
            /** If the password can not be verified */
            else {
                    $errors[] = "unknown user / password";
                }





        }
    }

    if ($input['UserType'] == "staff") {
        $sql = "SELECT ID, FirstName, LastName, Password
                       From ServiceUsers
                       WHERE EmailAddress = :username";
        $stmt = $dbConn->prepare($sql);
        $stmt->execute(array(':username' => $input['username']));

        $recordObj = $stmt->fetchObject();
        /** If statement to see if a row is returned */
        if ($recordObj) {
            $passwordHash = $recordObj->Password;
            /** Use password verify to make sure the password is correct and store data in the session */
            if (password_verify($input['password'], $passwordHash)) {
                $input['name'] = $recordObj->firstname;
                $_SESSION['user'] = $input['user'];
                $_SESSION['fName'] = $input['name'];
                $_SESSION['loggedIn'] = true;
                $_SESSION['lastTime'] = time(); // Use to check for inactivity

            }
            /** If the password can not be verified */
            else {
                $errors[] = "unknown user / password";
            }



        }

    }
    /** Stores the page that they logged in from */
    $input['page'] = $_SERVER['HTTP_REFERER'];

    return array($input, $errors);
}
function store_errors($errors){
    $_SESSION['errors'] = $errors;
}
