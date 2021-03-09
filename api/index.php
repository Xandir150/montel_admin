<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
require 'vendor/autoload.php';
// header("Access-Control-Allow-Headers: application/json, text/plain, */*");
// header('Access-Control-Allow-Methods: DELETE, POST, GET, OPTIONS');
// header("Access-Control-Allow-Headers: application/json, text/plain, */*");

function sendSingleSms($phone, $msg)
{
    $url = "https://semysms.net/api/3/sms.php"; //Url address for sending SMS
	$device = '275218';  //  Device code
	$token = '58e2b823555a62381c68ea8e68a57030';  //  Your token (secret)

	$data = array(
			"phone" => $phone,
			"msg" => $msg,
			"device" => $device,
			"token" => $token
		);

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);     
    $output = curl_exec($curl);
    curl_close($curl);

    //echo $output;
}

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
			if(strlen(explode(".",end(explode(" ", $request->files['files']["name"])))[0]) != 7) {
				echo 'ERROR INVOICE NUMBER';
				break;
			}
			$sql = "INSERT INTO adminweb.bills (`number`, `doc_num`, `amount`, `provider`, `calls_local`, 
										`calls_other`, `calls_landline`, `sms_national`, `sms_international`, 
										`gprs`, `calls_special`, `call_international`, `roaming`, `addational_service`, 
										`mms`, `over_limit`, `discount`, `service`,`month`) 
				VALUES (
					".$number.", '".explode(".",end(explode(" ", $request->files['files']["name"])))[0]."', ".$row['PRETPLATA'] * $pdv.", 'Mtel', 
					".$row['POZIVI_U_68_MREZI'] * $pdv.", ".$row['POZIVI_KA_DRUGIM_MREZAMA'] * $pdv.", 
					".$row['POZIVI_PREMA_FIKSNOJ_MREZI'] * $pdv.", ".$row['SMS_NACIONALNI'] * $pdv.", ".$row['SMS_INTERNACIONALNI'] * $pdv.", 
					".$row['GPRS'] * $pdv.", ".$row['POZIVI_PREMA_SPEC_BROJEVIMA'] * $pdv.", ".$row['POZIVI_INTERNACIONALNI'] * $pdv.", 
					".$row['ROMING'] * $pdv.", ".$row['DODATNE_USLUGE'] * $pdv.", ".$row['MMS'] * $pdv.", 
					".$row['POTROSNJA_PREKO_LIMITA'] * $pdv.", ".$row['POPUST'] * $pdv.", 'bill','".$row['MJESEC']."'
				);";
			$db->query($sql);
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
		$sql = "UPDATE adminweb.customers SET $option = '$value' WHERE id = $id LIMIT 1";
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
		if($id > 0) {
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
		else {
			$sql = "INSERT INTO  adminweb.`customers` 
			(`name`, `email`, `phone`, `city`, `telegram`, `Facebook`, `credit`, `description`, `tariff`, `status`) 
			VALUES ('$name', '$email', '$phone', '$city', '$telegram', '$Facebook', '0', '$description', '0', '1')";
			$db->query($sql);
			echo $db->insert_id;
		}
	}
});
Flight::route('GET /getBills', function () {
	$number = Flight::request()->query->number;
	$date = Flight::request()->query->date;
	$db = Flight::db();
	$sql = "select @row_num:= @row_num + 1 as row_num, d.*, customers.*, cb as balance from (
			select montel.payments.id, datetime, number, place, round(amount/100,2) as debt, '' as ccredit, provider, 'pay' as service, description as pdesc, cb from montel.payments union all
			select adminweb.bills.id, datetime, number, doc_num, '' as debt, tAmount as ccredit, provider, service, '' as pdesc, cb from adminweb.bills
			) as d inner join adminweb.customers on customers.phone = d.number, (SELECT @row_num:= 0 AS num) as r "
			.($number?" where number=$number":'').($date?" and `datetime` >= '$date'":'')." ORDER BY datetime desc";
			try {
		$result = $db->query($sql);
		echo '[';
		for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
			echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
		}
		echo ']';
	} catch (Exception $e) { echo $e; }
	
});
//список агрегация по периоду
Flight::route('GET /getInvoicesList', function () {
	$groupby = Flight::request()->query->groupby;
	$month = Flight::request()->query->month;
	if(!isset($groupby)) {
		echo 'не указан праметр group by';
		exit;
	}
	$db = Flight::db();
	$sql = "select @row_num:= @row_num + 1 as row_num, DATE(datetime) as date,".
			($groupby === 'month' ? " `month`, ":'').
			($groupby === 'doc_num' ? " `doc_num`, ":'').
			"sum(amount) as amount, provider, sum(discount) as discount,
			sum(cb) as client_balances,
			sum(OverFee) as overfee,
			sum(OverFeeTRate) as overfeetrate,
			sum(tAmount) as tamount,
			sum(revenue) as revenue
			from bills, (SELECT @row_num:= 0 AS num) as r ".
			($month ? " where `month` ='".$month."'":'').
			"GROUP by ".
			($groupby === 'month' ? " `month`, ":'').
			($groupby === 'doc_num' ? " `doc_num`, ":'').
			" provider,DATE(datetime) order by date desc";
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
	$month = Flight::request()->query->month;
	$doc_num = Flight::request()->query->doc;
	$service = Flight::request()->query->service;
	$where = false;
	if(isset($number) || isset($doc_num) || isset($service) || isset($month)){
		$where = true;
	}
	$sql = "select @row_num:= @row_num + 1 as row_num, bills.* from adminweb.bills, 
			(SELECT @row_num:= 0 AS num) as r ".($where?" where ":'')."1=1".($number?" and number=$number":'').($doc_num?" and doc_num='$doc_num'":'').($month?" and month='$month'":'').($service?" and service='$service'":'');
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
	$fromdate = Flight::request()->query->fromdate;
	$db = Flight::db();
	if(isset($id)) {
		if($dt == "pings")
			$sql = "SELECT datetime FROM montel.pings where id='$id' ORDER BY `datetime` DESC limit 1000";
		elseif ($dt == "payments") 
			$sql = "SELECT
					@row_num:= @row_num + 1 as row_num,
					montel.payments.datetime,
					montel.payments.amount,
					montel.payments.number,
					montel.payments.description,
					adminweb.customers.`name`
					FROM
					montel.payments
					RIGHT JOIN adminweb.customers ON montel.payments.number = adminweb.customers.phone,
					(SELECT @row_num:= 0 AS num) as r
					WHERE place = '$id'".($fromdate ? " and payments.datetime like '$fromdate%'":'')."
					ORDER BY datetime DESC
					LIMIT 1000;";
	}
	else {
		$sql = "SELECT pings.id, max(pings.datetime) as ping, payments.datetime as lastpay, collection.amount
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
Flight::route('GET /noTerminals', function () {
	$db = Flight::db();
	$sql = "SELECT
			@row_num:= @row_num + 1 as row_num,
			Max(payments.datetime) AS datetime,
			payments.place as id,
			collection.amount
			FROM
			montel.payments
			LEFT JOIN montel.collection ON payments.place = collection.place,
			(SELECT @row_num:= 0 AS num) as r
			where not exists(select * from montel.pings where payments.place= pings.id)
			group by payments.place limit 500;";
	try {
			$result = $db->query($sql);
			echo '[';
			for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
			echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
		}
			echo ']';
		} catch (Exception $e) { echo $e; }
});
Flight::route('GET /getCustomer', function () {
	$id = Flight::request()->query->id;
	$phone = Flight::request()->query->phone;
	$db = Flight::db();
	if(isset($id)) { 
		$where = "customers.id = $id";
	}
	if(isset($phone)) {
		$where = "customers.phone = $phone";
	}
	$sql = "SELECT *, ROUND(balance,2) as balance, ROUND(expenses,2) as cexpenses  FROM adminweb.customers
			where $where";
	try {
		$result = $db->query($sql);
		for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
			echo ($i > 0?',':'').json_encode(mysqli_fetch_object($result));
		}
	} catch (Exception $e) { echo $e; }
	
});
Flight::route('GET /getHysBalance', function () {
	$id = Flight::request()->query->id;
	$date = Flight::request()->query->date;
	$db = Flight::db();
	$sql = "SELECT balance FROM adminweb.balances_daily
			where `number` = '$id' and `date` like '$date%' LIMIT 1";
	try {
		$result = $db->query($sql);
		echo $result->fetch_object()->balance;
	} catch (Exception $e) { echo $e; }
	
});
Flight::route('GET /getOptions', function () {
	$id = Flight::request()->query->id;
	$db = Flight::db();
	$sql = "SELECT * FROM `settings`";
	try {
		$result = $db->query($sql);
		for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
			echo ($i > 0?',':'').json_encode(mysqli_fetch_object($result));
		}
	} catch (Exception $e) { echo $e; }
	
});
Flight::route('GET /getPayMethods', function () {
	$db = Flight::db();
	$sql = "SELECT * FROM montel.`payment_methods`";
	try {
			$result = $db->query($sql);
			echo '[';
			for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
			echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
		}
			echo ']';
	} catch (Exception $e) { echo $e; }
	
});
Flight::route('GET /outbox_sms', function () {	
	// $request = Flight::request()->query;
	// $number = $request->number;
	// if(substr( $number, 0, 3 ) === "382")
	// 	$number = substr( $number, 3, 8 );
	echo file_get_contents("https://semysms.net/api/3/outbox_sms.php?token=58e2b823555a62381c68ea8e68a57030&device=274663"); //1136197136
});
Flight::route('GET /inbox_sms', function () {	
	// $request = Flight::request()->query;
	// $number = $request->number;
	// if(substr( $number, 0, 3 ) === "382")
	// 	$number = substr( $number, 3, 8 );
	echo file_get_contents("https://semysms.net/api/3/inbox_sms.php?token=58e2b823555a62381c68ea8e68a57030&start_id=1");
});
Flight::route('GET /dashboardGetInvByMonth', function () {
	$id = Flight::request()->query->id;
	$db = Flight::db();
	$sql = "select * from (
			select DATE_FORMAT(STR_TO_DATE(`month`, '%m'),'%b') as im, sum(amount) as iamount, `month`
			from adminweb.bills 
			where YEAR(datetime) = YEAR(CURDATE()) group by `month`) as i
			inner JOIn (
			select DATE_FORMAT(datetime,'%b') as pm, sum(amount) / 100 as pamount from montel.payments 
			where provider = 'montel' and YEAR(datetime) = YEAR(CURDATE()) group by DATE_FORMAT(datetime,'%b')) as p on pm=im order by `month`"; //сделать не текущий год, а прошлые 12 мес
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
Flight::route('GET /dashboardCollectSum', function () {
	$id = Flight::request()->query->id;
	$db = Flight::db();
	// $sql = "select sum(amount) as amount
	// 		FROM
	// 		montel.collection where EXISTS(select * from montel.pings where pings.id = place)";
	$sql = "select sum(amount) as amount
			FROM
			montel.collection where place not like 'Корректировка (списание)'";
	try {
		$result = $db->query($sql);
		echo mysqli_fetch_object($result)->amount;
	} catch (Exception $e) { echo $e; }
	
});
Flight::route('GET /dashboardNewCustomers', function () {
	$id = Flight::request()->query->id;
	$db = Flight::db();
	$sql = "SELECT count(*) as amount FROM `customers` WHERE MONTH(created) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
	try {
		$result = $db->query($sql);
		echo mysqli_fetch_object($result)->amount;
	} catch (Exception $e) { echo $e; }
	
});
Flight::route('GET /dashboardTotalCustomers', function () {
	$id = Flight::request()->query->id;
	$db = Flight::db();
	$sql = "SELECT count(*) as amount FROM `customers` WHERE status = 1";
	try {
		$result = $db->query($sql);
		echo mysqli_fetch_object($result)->amount;
	} catch (Exception $e) { echo $e; }
	
});
Flight::route('GET /tmpxls', function () {
	$key = Flight::request()->query->key;
	$db = Flight::db();
	if($key === 'NQ7F45968eZYa2A442gwY55SteYhUAnvjKhaz6m6') {
		$sql = "SELECT
				customers.phone,
				customers.`name`,
				round(cb+tAmount,1) as lastCb,
				round(amount*1.21+tFixAdd,1) as lastTarif,
				OverFeeTRate as OverTariff,
				tOperatorFee+tFixAdd as nextTariff,
				(cb+tAmount)-ROUND(amount*1.21+tFixAdd,1)-OverFeeTRate-(tOperatorFee+tFixAdd) as toPpay,
				p.pay
				FROM
				customers
				INNER JOIN tariffs ON customers.tariff = tariffs.id
				INNER JOIN bills ON customers.phone = bills.number
				LEFT JOIN (select sum(amount) /100 as pay, number from montel.payments where amount > 0  and datetime > '2021-03-01' group by number ) as p on p.number = customers.phone
				where `month` = '2,2021' and `status` = 1 and tFixAdd <> 0";
	}
	try {
		$result = $db->query($sql);
		$all_property = array();  //declare an array for saving property

		//showing property
		echo '<table class="data-table">
				<tr class="data-heading">';  //initialize table tag
		while ($property = mysqli_fetch_field($result)) {
			echo '<td>' . $property->name . '</td>';  //get field name for header
			array_push($all_property, $property->name);  //save those to array
		}
		echo '</tr>'; //end tr tag

		//showing all data
		while ($row = mysqli_fetch_array($result)) {
			echo "<tr>";
			foreach ($all_property as $item) {
				echo '<td>' . $row[$item] . '</td>'; //get items using property value
			}
			echo '</tr>';
		}
		echo "</table>";
	} catch (Exception $e) { echo $e; }
	
});
Flight::route('GET /customers', function () {
	$filter = Flight::request()->query->filter;
	$val = Flight::request()->query->val;
	if($filter == "balance" && $val == 1)
		$where = "ROUND(balance,2) < 0";
	elseif($filter == "balance" && $val == 2)
		$where = "status = 1"; //ROUND(payments-expenses,2) >= 0 and 
	elseif($filter == "new")
		$where = " `status` = '1' AND (MONTH(created) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) or name = '')";
	$db = Flight::db();
	$sql = "select id,credit,name,email,phone,status,ROUND(balance,2) as balance, Facebook,description, tariff, created, payments, ROUND(expenses,2) as expenses  
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
	// echo file_get_contents("http://www.rusgruppa.me/testPhone.php?phone=$number");
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
			VALUES ('$number','$place', $amount, '$provider', $ip);";
	$db->query($sql);
	$sql = "select round(cb,2) as balance from payments where number = '".$number."' ORDER BY id DESC limit 1;";
	try {
		$result = $db->query($sql);
		sendSingleSms("0".$number, "На ваш счёт зачислено ". ($amount / 100) ."€. Баланс ".$result->fetch_object()->balance."€");
	} catch (Exception $e) {  }
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
	if(isset($number)) {
		$db = Flight::db();
		$sql = "INSERT INTO montel.payments (number, place, amount, provider, description)
				VALUES ('$number','$place', $amount * 100, '$provider', '$description')";
		
		$sql2 = "select round(balance,2) as balance from adminweb.customers where phone = '".$number."' limit 1;";
		try {
			$result = $db->query($sql2);
			sendSingleSms("0".$number, "На ваш счёт зачислено ". ($amount) ."€."); //todo balance
		} catch (Exception $e) {  }
		echo $db->query($sql);
	}
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
				customers.*, ROUND(customers.balance, 2) as balance
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