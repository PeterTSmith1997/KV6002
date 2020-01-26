<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 09/12/2019
 * Time: 22:20
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");

$data = json_decode(file_get_contents("php://input"));
