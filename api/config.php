<?php

$host = "127.0.0.1"; /* Host name */
$user = "adminmontel"; /* User */
$password = "V#t5t4j7"; /* Password */
$dbname = "adminweb"; /* Database name */

$con = mysqli_connect($host, $user, $password,$dbname);
// Check connection
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}