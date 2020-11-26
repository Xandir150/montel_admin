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
      $id = $_GET['id'];
      $debts = $_GET['debts'];
      if(isset($_GET['tariffs'])){
        $sql = "select id as value, name as text from tariffs";
      }
      elseif($debts == 0) { 
        $sql = "select id,credit,name,email,phone,status,ROUND(payments-expenses,2) as balance, Facebook, tariff, created, payments, ROUND(expenses,2) as expenses  from customers".($id?" where id=$id":'');
      }
      elseif($debts == 1) {
        $sql = "select id,credit,name,email,phone,status,ROUND(payments-expenses,2) as balance, Facebook, tariff, created, payments, ROUND(expenses,2) as expenses  from customers where ROUND(payments-expenses,2) < 0";
      }
      else {
        $sql = "select id,credit,name,email,phone,status,ROUND(payments-expenses,2) as balance, Facebook, tariff, created, payments, ROUND(expenses,2) as expenses  from customers where ROUND(payments-expenses,2) >= 0";
      }
      //@depricated see index
      if(isset($_GET['terminals'])){
        $sql = "select pings.id, max(pings.datetime) as ping, payments.datetime as lastpay, collection.amount
                FROM
                montel.pings
                INNER JOIN (SELECT max(datetime) as datetime, place from montel.payments group by place) AS payments ON payments.place= pings.id
                RIGHT JOIN montel.collection ON collection.place= pings.id
                group by pings.id";
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