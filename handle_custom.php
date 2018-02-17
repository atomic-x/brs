<?php
include_once("includes/db_connect.php");

//I like to turn on errors so that I can see what is going on if all goes wrong.
  ini_set('display_errors', 1);
  error_reporting(E_ALL);

//this gets everything after www.mysite.com/
//so if the url was www.mysite.com/my-page then page would = "my-page"
//the escape string function prevents sql injection attack
$page = "index.php";
$path = "";
$cid = "";
$comp_id = "";

if (isset($_GET['path'])) {
	$page = mysql_real_escape_string($_GET['path']);
	echo $page;
	die();
}

if (isset($_GET['cid'])) {
	$cid = mysql_real_escape_string($_GET['cid']);
}

if (isset($_GET['comp_id'])) {
	$comp_id = mysql_real_escape_string($_GET['comp_id']);
}

$checkPages = explode("/", $page);

if (count($checkPages)) {
	if ($checkPages[0] == "manage") {
		header("location: /manage/index.php");
		die();
	}else if ($checkPages[0] == "company") {
		header("location: /company/index.php");
		die();
	}else if ($checkPages[0] == "competitions") {
		header("location: /competitions/index.php");
		die();
	}
}

$query = "Select cid From company_path Where path='$page' Limit 1;";

list($path) = mysql_fetch_array(mysql_query($query));

if ($path){
	header("Location: c/index.php?cid=$path");
	die();
}else if ($cid){
	header("Location: c/index.php?cid=$cid");
	die();
}else if ($comp_id){
	header("Location: competitions/index.php?comp_id=$comp_id");
	die();
}else{
	$query = "Select comp_id From competition_path Where path='$page' Limit 1;";
	
	list($path) = mysql_fetch_array(mysql_query($query));
	
	if ($path){
		header("Location: competitions/index.php?comp_id=$path");
		die();
	}else{
		header("Location: index.php");
		die();
	}
}
?>