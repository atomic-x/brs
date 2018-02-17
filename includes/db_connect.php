<?
session_start();

$db = @mysql_connect("localhost","business_dbowner","Yell0wSt0ne!");

if (!$db) {
	echo( "<p class=\"copy\">Unable to connect to the database server at this time.</p>" );
	exit();
}

if (!@mysql_select_db("business_db")) {
	echo( "<p class=\"copy\">Unable to locate the database at this time.</p>" );
	exit();
}

if (!isset($_SESSION))
	session_start();
	
function errorState() {
	if (strlen($_SESSION['message']) > 0) {
		echo $_SESSION['message'];
		unset($_SESSION['message']);
		return true;
	}else{
		return false;
	}
}

?>