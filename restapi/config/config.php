<?php

class Database
{

	/*private $host = "localhost";
	private $user = "root";
	private $pass = "";
	private $dbname = "govindlal_college";*/
	 private $host = "localhost";
	 private $user = "alumnipo_user";
	 private $pass = "User@123A";
	 private $dbname = "alumnipo_gdb";

	public function getConnection()
	{
		$conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		if ($conn->connect_error) {
			die("Error failed to connect to MySQL: " . $conn->connect_error);
		} else {
			return $conn;
		}
	}

}
// $host="localhost";
// $user="alumnipo_user";
// $pass="User@123A";
// $dbname="alumnipo_gdb";
// $GLOBALS['conn'] = mysqli_connect($host, $user, $pass, $dbname);

/*$host="localhost";
$user="ramanart_user";
$pass="User@123";
$dbname="ramanart_vida";
$GLOBALS['conn'] = mysqli_connect($host, $user, $pass, $dbname);*/

/*$host="localhost";
$user="gmprojec_user";
$pass="Shyam*123";
$dbname="gmprojec_finance";
$GLOBALS['conn'] = mysqli_connect($host, $user, $pass, $dbname);*/


// function getpre()
// {
// 	$qpre="select pre from admin";
// $rpre=mysqli_query($GLOBALS['conn'],$qpre) or die(mysqli_error($GLOBALS['conn']));
// $rowpre=mysqli_fetch_row($rpre);
// return $rowpre[0];
// }

// function getagentpre()
// {
// 	return "SBA";
// }

// function getcompany()
// {
// 	$qpre="select company from admin";
// $rpre=mysqli_query($GLOBALS['conn'],$qpre) or die(mysqli_error($GLOBALS['conn']));
// $rowpre=mysqli_fetch_row($rpre);
// return $rowpre[0];
// }

// function getcity($id)
// {

// 	if($id==""){
// 		$qpre="select city from admin where id='1' ";
// 	}else{
// 		$qpre="select city from admin where id='$id' ";
// 	}
// $rpre=mysqli_query($GLOBALS['conn'],$qpre) or die(mysqli_error($GLOBALS['conn']));
// $rowpre=mysqli_fetch_row($rpre);
// return $rowpre[0];
// }

// function getaddress($id){		
// 	if($id==""){
// 		$qpre="select city from admin where id='1' ";
// 	}else{
// 		$qpre="select city from admin where id='$id' ";
// 	}
// $rpre=mysqli_query($GLOBALS['conn'],$qpre) or die(mysqli_error($GLOBALS['conn']));
// $rowpre=mysqli_fetch_row($rpre);
// return $rowpre[0];
// }

// function getOfficeStatus(){
// 	$qpre="select open from admin where id='1' ";

// $rpre=mysqli_query($GLOBALS['conn'],$qpre) or die(mysqli_error($GLOBALS['conn']));
// $rowpre=mysqli_fetch_row($rpre);
// return $rowpre[0];
// }

// function sendSMS($msg,$mobile){

// 	$username="suvarnabhumi";
// 	$password="suvarnabhumi2018";
// 	$senderid="SBHUMI";

// 	$url="https://smsozone.com/api/mt/SendSMS?user=$username&password=$password&senderid=$senderid&channel=Trans&DCS=0&flashsms=0&number=".$mobile."&text=".rawurlencode($msg)."&route=1";

// 	$ch=curl_init($url);
// 	curl_setopt($ch, CURLOPT_URL, $url);
// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// 	$curl_scraped_page = curl_exec($ch);
// 	curl_close($ch);
// }

// function getSMSBalance(){

// 	$username="suvarnabhumi";
// 	$password="suvarnabhumi2018";
// 	$senderid="SBHUMI";
// 	$url="https://www.smsozone.com/api/mt/GetBalance?User=$username&Password=$password";

// 	$ch=curl_init($url);
// 	curl_setopt($ch, CURLOPT_URL, $url);
// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// 	$balance = curl_exec($ch);
// 	curl_close($ch);
// 	return $balance;
// }
?>