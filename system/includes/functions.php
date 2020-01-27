<?php
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
<body>";

    return $header;
}