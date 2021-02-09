<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
require 'vendor/autoload.php';
// header("Access-Control-Allow-Headers: application/json, text/plain, */*");
// header('Access-Control-Allow-Methods: DELETE, POST, GET, OPTIONS');
// header("Access-Control-Allow-Headers: application/json, text/plain, */*");


Kint::enabled(true);

Flight::register('db', 'mysqli', array('localhost','adminmontel','V#t5t4j7','adminweb'));

Flight::route('GET /', function(){
    echo 'Hello world!';
});
Flight::route('POST /', function(){
    echo 'I received either a POST request.';
});
Flight::route('POST|OPTIONS /uploadInvoice', function(){
	$request = Flight::request();
	if ( $xlsx = SimpleXLSX::parse($request->files['files']['tmp_name']) ) {
		$header_values = $rows = [];
        foreach ( $xlsx->rows() as $k => $r ) {
            if ( $k === 0 ) {
                $header_values = $r;
                continue;
            }
			$rows[] = array_combine( $header_values, $r );
        }
		foreach($rows as $row){
			$number = $row['PRETPLATNICKI_BROJ'];
			$pdv = 1;
			if(substr( $number, 0, 3 ) === "382")
				$number = substr( $number, 3, 8 );
			$db = Flight::db();
			$sql = "INSERT INTO adminweb.bills (`number`, `doc_num`, `amount`, `provider`, `calls_local`, 
										`calls_other`, `calls_landline`, `sms_national`, `sms_international`, 
										`gprs`, `calls_special`, `call_international`, `roaming`, `addational_service`, 
										`mms`, `over_limit`, `discount`, `service`) 
				VALUES (
					".$number.", '".$row['MJESEC']."', ".$row['PRETPLATA'] * $pdv.", 'Mtel', 
					".$row['POZIVI_U_68_MREZI'] * $pdv.", ".$row['POZIVI_KA_DRUGIM_MREZAMA'] * $pdv.", 
					".$row['POZIVI_PREMA_FIKSNOJ_MREZI'] * $pdv.", ".$row['SMS_NACIONALNI'] * $pdv.", ".$row['SMS_INTERNACIONALNI'] * $pdv.", 
					".$row['GPRS'] * $pdv.", ".$row['POZIVI_PREMA_SPEC_BROJEVIMA'] * $pdv.", ".$row['POZIVI_INTERNACIONALNI'] * $pdv.", 
					".$row['ROMING'] * $pdv.", ".$row['DODATNE_USLUGE'] * $pdv.", ".$row['MMS'] * $pdv.", 
					".$row['POTROSNJA_PREKO_LIMITA'] * $pdv.", ".$row['POPUST'] * $pdv.", 'bill'
				)
				ON DUPLICATE KEY UPDATE amount = VALUES(amount);";
			// echo $sql;
			// 	break;
			$db->query($sql);
			//$db->query("TRUNCATE TABLE adminweb.bills_blackhole;");
		}
		echo count($rows); //чушь. переделать.
	} else {
		echo SimpleXLSX::parseError();
	}
});
Flight::route('POST|OPTIONS /setCustomerOption', function(){
	$request = Flight::request()->data->getData();
	$id = $request['id'];
	$option = $request['option'];
	$value = $request['value'];
	if(isset($id) && isset($option) && isset($value)) {
		$db = Flight::db();
		$sql = "UPDATE adminweb.customers SET $option = $value WHERE id = $id LIMIT 1";
		echo $db->query($sql);
	}
});
Flight::route('POST|OPTIONS /newTariff', function(){
	$request = Flight::request()->data->getData();
	$id = $request['id'];
	$newTariffId = $request['newTariffId'];
	if(isset($id) && isset($newTariffId)) {
		$db = Flight::db();
		$sql = "UPDATE adminweb.customers SET tariff = $newTariffId WHERE id = $id LIMIT 1";
		echo $db->query($sql);
	}
});
Flight::route('POST|OPTIONS /updateProfile', function(){
	$request = Flight::request()->data->getData();
	$id = $request['id'];
	$name = $request['name'];
	$email = $request['email'];
	$phone = $request['phone'];
	$city = $request['city'];
	$telegram = $request['telegram'];
	$Facebook = $request['Facebook'];
	$credit = $request['credit'];
	$description = $request['description'];
	$status = $request['status'];
	$tPercent = $request['tPercent'];
	$tDicsount = $request['tDicsount'];
	if(isset($id) && isset($name)) {
		$db = Flight::db();
		$sql = "UPDATE adminweb.customers SET 
				`name` = '$name',
				email = '$email',
				phone = '$phone',
				city = '$city',
				telegram = '$telegram',
				Facebook = '$Facebook',
				credit = '$credit',
				`description` = '$description',
				`status` = '$status',
				tPercent = '$tPercent',
				tDicsount = '$tDicsount'
				WHERE id = '$id' LIMIT 1";
		echo $db->query($sql);
	}
});
Flight::route('GET /getBills', function () {
	$number = Flight::request()->query->number;
	$db = Flight::db();
	$sql = "select @row_num:= @row_num + 1 as row_num, d.*, customers.*, cb as balance from (
			select montel.payments.id, datetime, number, place, round(amount/100,2) as debt, '' as ccredit, provider, 'pay' as service, description as pdesc, cb from montel.payments union all
			select adminweb.bills.id, datetime, number, doc_num, '' as debt, tAmount as ccredit, provider, service, '' as pdesc, cb from adminweb.bills
			) as d inner join adminweb.customers on customers.phone = d.number, (SELECT @row_num:= 0 AS num) as r ".($number?" where number=$number":'')." ORDER BY datetime";
			try {
		$result = $db->query($sql);
		echo '[';
		for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
			echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
		}
		echo ']';
	} catch (Exception $e) { echo $e; }
	
});
Flight::route('GET /getInvoicesList', function () {
	$number = Flight::request()->query->number;
	$db = Flight::db();
	$sql = "select @row_num:= @row_num + 1 as row_num, DATE(datetime) as date,doc_num,sum(amount) as amount, provider, sum(discount) as discount,
			sum(cb) as client_balances,
			sum(OverFee) as overfee,
			sum(OverFeeTRate) as overfeetrate,
			sum(tAmount) as tamount,
			sum(revenue) as revenue
			from bills, (SELECT @row_num:= 0 AS num) as r
			GROUP by doc_num, provider,DATE(datetime)";
			try {
		$result = $db->query($sql);
		echo '[';
		for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
			echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
		}
		echo ']';
	} catch (Exception $e) { echo $e; }
	
});
Flight::route('GET /invoices', function () {
	$db = Flight::db();
	$number = Flight::request()->query->number;
	$doc_num = Flight::request()->query->doc;
	$service = Flight::request()->query->service;
	$where = false;
	if(isset($number) || isset($doc_num) || isset($service)){
		$where = true;
	}
	$sql = "select @row_num:= @row_num + 1 as row_num, bills.* from adminweb.bills, 
			(SELECT @row_num:= 0 AS num) as r ".($where?" where ":'')."1=1".($number?" and number=$number":'').($doc_num?" and doc_num='$doc_num'":'').($service?" and service='$service'":'');
	if(isset($number)) {
			try {
			$result = $db->query($sql);
			for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
				echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
			}
		} catch (Exception $e) { echo $e; }
	} else {
			try {
			$result = $db->query($sql);
			echo '[';
			for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
				echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
			}
			echo ']';
		} catch (Exception $e) { echo $e; }
	}
});
Flight::route('GET /tariffs', function () {
	$db = Flight::db();
	$sql = "SELECT * FROM adminweb.tariffs";
	try {
		$result = $db->query($sql);
		echo '[';
		for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
			echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
		}
		echo ']';
	} catch (Exception $e) { echo $e; }
});
Flight::route('GET /terminals', function () {
	$id = Flight::request()->query->id;
	$dt = Flight::request()->query->dt;
	$db = Flight::db();
	if(isset($id)) {
		if($dt == "pings")
			$sql = "select datetime FROM montel.pings where id='$id' ORDER BY `datetime` DESC limit 1000";
		elseif ($dt == "payments") 
			$sql = "select datetime, amount, number from montel.payments WHERE place = '$id' ORDER BY datetime DESC LIMIT 1000";
	}
	else {
		$sql = "select pings.id, max(pings.datetime) as ping, payments.datetime as lastpay, collection.amount
					FROM
					montel.pings
					inner JOIN (SELECT max(datetime) as datetime, place from montel.payments group by place) AS payments ON payments.place= pings.id
					LEFT JOIN montel.collection ON collection.place= pings.id
					group by pings.id;";
	}
	try {
			$result = $db->query($sql);
			echo '[';
			for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
			echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
		}
			echo ']';
		} catch (Exception $e) { echo $e; }
});
Flight::route('GET /getCustomerById', function () {
	$id = Flight::request()->query->id;
	$db = Flight::db();
	$sql = "SELECT *, ROUND(payments-expenses,2) as balance, ROUND(expenses,2) as cexpenses  FROM adminweb.customers
			where customers.id = ".$id;
	try {
		$result = $db->query($sql);
		for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
			echo ($i > 0?',':'').json_encode(mysqli_fetch_object($result));
		}
	} catch (Exception $e) { echo $e; }
	
});
Flight::route('GET /dashboardGetInvByMonth', function () {
	$id = Flight::request()->query->id;
	$db = Flight::db();
	$sql = "select * from (
			select DATE_FORMAT(STR_TO_DATE(doc_num, '%m'),'%b') as im, sum(amount) as iamount, doc_num
			from adminweb.bills 
			where YEAR(datetime) = YEAR(CURDATE()) group by doc_num) as i
			inner JOIn (
			select DATE_FORMAT(datetime,'%b') as pm, sum(amount) / 100 as pamount from montel.payments 
			where provider = 'montel' and YEAR(datetime) = YEAR(CURDATE()) group by DATE_FORMAT(datetime,'%b')) as p on pm=im order by doc_num"; //сделать не текущий год, а прошлые 12 мес
	try {
		$result = $db->query($sql);
		// data: {
        //     labels: ['Ja', 'Fe', 'Ma', 'Ap', 'Mai', 'Ju', 'Jul', 'Au', 'Se', 'Oc', 'No', 'De'],
        //     series: [
        //       [542, 443, 320, 780, 553, 453, 326, 434, 568, 610, 756, 895],
        //       [230, 750, 450, 300, 280, 240, 200, 190],
        //     ],
		//   },
		while($row = $result->fetch_array())
		{
			$rows[] = $row;
		}
		echo '{ "labels": [ ';
		for ($i=0 ; $i<count($rows) ; $i++){
			echo ($i>0?',':'').'"'.$rows[$i]['im'].'"';
		}
		echo '], ';
		echo '"series": [';
				echo '[';
				for ($i=0 ; $i<count($rows) ; $i++){
					echo ($i>0?',':'').$rows[$i]['iamount'];
				}
				echo '], ';
				echo '[';
				for ($i=0 ; $i<count($rows) ; $i++){
					echo ($i>0?',':'').$rows[$i]['pamount'];
				}
				echo '] ';
		echo '] ';
		echo '}';
	} catch (Exception $e) { echo $e; }
	
});
Flight::route('GET /dashboardGetCacheInterms', function () {
	$id = Flight::request()->query->id;
	$db = Flight::db();
	$sql = "select sum(amount) as amount from montel.collection where place != 'TEST'";
	try {
		$result = $db->query($sql);
		echo mysqli_fetch_object($result)->amount;
	} catch (Exception $e) { echo $e; }
	
});
Flight::route('GET /customers', function () {
	$filter = Flight::request()->query->filter;
	$val = Flight::request()->query->val;
	if($filter == "balance" && $val == 1)
		$where = "ROUND(payments-expenses,2) < 0";
	elseif($filter == "balance" && $val == 2)
		$where = "status = 1"; //ROUND(payments-expenses,2) >= 0 and 
	elseif($filter == "new")
		$where = " `status` = '1' AND (`lastpaydate` IS NULL OR `payments` = '0' AND `phone` < '50000') ";
	$db = Flight::db();
	$sql = "select id,credit,name,email,phone,status,ROUND(payments-expenses,2) as balance, Facebook, tariff, created, payments, ROUND(expenses,2) as expenses  
			from customers".($where?" WHERE $where":'');
	try {
		$result = $db->query($sql);
		echo '[';
		for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
			echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
		}
		echo ']';
	} catch (Exception $e) { echo $e; }
	
});
Flight::route('GET /getUserInfo', function () {	
	//TODO: сделать запрос имени и долга по номеру телефона. Ответ стрингом.
	$request = Flight::request()->query;
	$number = $request->number;
	if(substr( $number, 0, 3 ) === "382")
		$number = substr( $number, 3, 8 );
	echo file_get_contents("http://www.rusgruppa.me/testPhone.php?phone=$number");
});
Flight::route('GET /test', function () {	
	//TODO: сделать запрос имени и долга по номеру телефона. Ответ стрингом.
	$request = Flight::request()->query;
	$number = $request->number;
	if(substr( $number, 0, 3 ) === "382")
		$number = substr( $number, 3, 8 );
	$answer = file_get_contents("http://www.rusgruppa.me/testPhone.php?phone=$number");
	echo explode(" ", $answer)[4];
	
});
Flight::route('GET /ping', function () {
	$id = Flight::request()->query->id;
	$db = Flight::db();
	$sql = "INSERT INTO pings (id)
			VALUES ('$id')";
	try {
		$db->query($sql);
		//$data = file_get_contents("http://www.rusgruppa.me/smsApiX.php?go=Sms&in=terminal&master=$id");
		echo 'ok';
	} catch (Exception $e) { echo $e; }
	
});

Flight::route('POST /charge', function(){
	//$apr1$htllufe6$GkmB6xbt5y.iv/JbqCy1z/
	$request = Flight::request();
	$data = $request->data->getData();
	$number = $data['number'];
	if(substr( $number, 0, 3 ) === "382")
		$number = substr( $number, 3, 8 );
	$place = $data['place'];
	$amount = $data['amount'];
	$provider = $data['provider'];
	$ip = ip2long($request->ip) ;
    $db = Flight::db();
	$sql = "INSERT INTO montel.payments (number, place, amount, provider, ip)
			VALUES ('$number','$place', $amount, '$provider', $ip)";
	$db->query($sql);
	echo 'ok';
});
Flight::route('POST|OPTIONS /chargeCustom', function(){
	//$apr1$htllufe6$GkmB6xbt5y.iv/JbqCy1z/
	$request = Flight::request()->data->getData();
	$number = $request['number'];
	$place = $request['place'];
	$amount = $request['amount'];
	$provider = $request['provider'];
	$description = $request['description'];
    $db = Flight::db();
	$sql = "INSERT INTO montel.payments (number, place, amount, provider, description)
			VALUES ('$number','$place', $amount * 100, '$provider', '$description')";
	echo $db->query($sql);
});
///user lk
Flight::route('GET /getuserinfo2', function () {
	$db = Flight::db();
	$number = Flight::request()->query->number;
	$sql = "SELECT
			max(b.datetime) AS m,
				ROUND(b.amount) AS amount,
				ROUND(
					b.calls_local + b.calls_other + b.calls_landline + b.calls_special + b.call_international
				,3) AS calls,
				ROUND(
					b.sms_international + b.sms_national
				,3) AS sms,
				ROUND(b.gprs,3) as gprs,
				ROUND(b.roaming,3) as roaming,
				ROUND(
					b.over_limit + b.addational_service
				,3) AS services,
				customers.*, ROUND(customers.payments - customers.expenses, 2) as balance
			FROM
			bills as b
			RIGHT JOIN customers ON b.number = customers.phone
			WHERE
				customers.phone =  '$number'";
	try {
		$result = $db->query($sql);
		//echo '[';
		for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
			echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
		}
		//echo ']';
	} catch (Exception $e) { echo $e; }
});
Flight::start();