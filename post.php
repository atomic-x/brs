<?
include("includes/db_connect.php");
include("includes/functions.php");

if (isset($_POST['action'])){
    $action = mysql_real_escape_string($_POST['action']);
}else{
    $action = mysql_real_escape_string($_GET['action']);
}

if($action == "set_browser_location"){
    extract(cleanArray($_POST));
    
    $radius = 2;
    
    $lon1 = $longitude - $radius / abs(cos(deg2rad($latitude))*69);
    $lon2 = $longitude + $radius / abs(cos(deg2rad($latitude))*69);
    $lat1 = $latitude - ($radius/69);
    $lat2 = $latitude + ($radius/69);
    
    $query = "SELECT ZipCode ,
	    (3958*3.1415926*sqrt((latitude-$latitude)*(latitude-$latitude) + cos(latitude/57.29578)*cos($latitude/57.29578)*(longitude-$longitude)*(longitude-$longitude))/180)
	    as distance
	    FROM zipCodes
	    WHERE (3958*3.1415926*sqrt((latitude-$latitude)*(latitude-$latitude) + cos(latitude/57.29578)*cos($latitude/57.29578)*(longitude-$longitude)*(longitude-$longitude))/180) <= '$radius'
	    And longitude between $lon1 and $lon2 and latitude between $lat1 and $lat2
	    ORDER BY distance LIMIT 1";
	    
    list($zip, $distance) = mysql_fetch_array(mysql_query($query));
    
    $_SESSION['location'] = array("latitude" => $latitude, "longitude" => $longitude, "zip_code" => $zip);
    $_SESSION['zip'] = $zip;
    
    $_SESSION['browser_location'] = true;
}

if($action == "set_ip_location"){
    $_SESSION['location'] = get_user_location();
}
?>