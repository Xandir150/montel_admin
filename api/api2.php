<?php
include "config.php";
header('Access-Control-Allow-Origin: *');
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
//$input = json_decode(file_get_contents('php://input'),true);


if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}


switch ($method) {
    case 'GET':
      $customerid = $_GET['customerid'];
      if(isset($_GET['all'])){
        $sql = "select * from (
                select montel.payments.id, datetime, number, place, amount, provider, '' as service, 1 as debt from montel.payments union all
                select adminweb.bills.id, datetime, number, doc_num, amount, provider, service, 0 as debt from adminweb.bills
                ) as d";
      }
      break;
    case 'POST':
      $name = $_POST["name"];
      $email = $_POST["email"];
      $country = $_POST["country"];
      $city = $_POST["city"];
      $job = $_POST["job"];

      $sql = "insert into contacts (name, email, city, country, job) values ('$name', '$email', '$city', '$country', '$job')"; 
      break;
}

// run SQL statement
$result = mysqli_query($con,$sql);

// die if SQL statement failed
if (!$result) {
  http_response_code(404);
  die(mysqli_error($con));
}

if ($method == 'GET') {
    if (!$id) echo '[';
    for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
      echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
    }
    if (!$id) echo ']';
  } elseif ($method == 'POST') {
    echo json_encode($result);
  } else {
    echo mysqli_affected_rows($con);
  }

$con->close();