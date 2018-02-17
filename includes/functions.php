<?php
include_once("includes/db_connect.php");
//include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/db_connect.php");
//include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/xmlParser.php");
//include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/bitly.php");
//include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/Image.php");
//include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/Auth.php");
//include_once($_SERVER['DOCUMENT_ROOT'] . "/facebook/facebook.php");

if (!$db) {
	include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/db_connect.php");
}

$state_list = array('AL'=>"Alabama",'AK'=>"Alaska", 'AZ'=>"Arizona", 'AR'=>"Arkansas", 'CA'=>"California", 'CO'=>"Colorado", 'CT'=>"Connecticut", 'DE'=>"Delaware",
		   'DC'=>"District Of Columbia", 'FL'=>"Florida", 'GA'=>"Georgia", 'HI'=>"Hawaii", 'ID'=>"Idaho", 'IL'=>"Illinois", 'IN'=>"Indiana", 'IA'=>"Iowa",
		   'KS'=>"Kansas", 'KY'=>"Kentucky", 'LA'=>"Louisiana", 'ME'=>"Maine", 'MD'=>"Maryland", 'MA'=>"Massachusetts", 'MI'=>"Michigan", 'MN'=>"Minnesota",
		   'MS'=>"Mississippi", 'MO'=>"Missouri", 'MT'=>"Montana",'NE'=>"Nebraska",'NV'=>"Nevada",'NH'=>"New Hampshire",'NJ'=>"New Jersey",'NM'=>"New Mexico",
		   'NY'=>"New York",'NC'=>"North Carolina",'ND'=>"North Dakota",'OH'=>"Ohio", 'OK'=>"Oklahoma", 'OR'=>"Oregon", 'PA'=>"Pennsylvania", 'RI'=>"Rhode Island",
		   'SC'=>"South Carolina", 'SD'=>"South Dakota",'TN'=>"Tennessee", 'TX'=>"Texas", 'UT'=>"Utah", 'VT'=>"Vermont", 'VA'=>"Virginia", 'WA'=>"Washington",
		   'WV'=>"West Virginia", 'WI'=>"Wisconsin", 'WY'=>"Wyoming");

function insertError($error) {
	$error = cleanArray($error);
	mysql_query("Insert Into error_log (title, description, error_date) Values ('".$error['title']."', '".$error['desc']."', now())");
	return;
}

function logout() {
	session_destroy();
	
	foreach ($_SESSION as $key => $var) {
		unset($_SESSION[$key]);
	}
	foreach ($_COOKIE as $key => $var) {
		unset($_COOKIE[$key]);
	}
	
	// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
		'appId'  => '209701079166123',
		'secret' => '8dff2f32e16cc0a9351b859b96f9db3b'
	));
	
	// Get User ID
	$user = $facebook->getUser();
	
	if($user){
	    try{
		$user_profile = $facebook->api('/me');
		//ovewrites the cookie
		$facebook->setSession(null);
	    }catch(FacebookApiException $e){
		$user = NULL;
	    }
	}
	
	header("Location: /index.php");
}

function checkAliasExists($alias = null) {
	list($count) = mysql_fetch_array(mysql_query("Select count(*) as aliasCount From company_alias Where alias='$alias';"));
	
	if (intval($count)) {
		return true;
	}else{
		return false;
	}
}

function checkAliasIsValid($alias = null) {
	if (stristr($alias, ".com") || stristr($alias, ".php") || stristr($alias, ".html") || stristr($alias, ".js") || stristr($alias, ".htm") ||
	    stristr($alias, ".phtml") || preg_match("/[^\da-zA-Z_-\s]/", $alias) || file_exists("/".$alias)) {
		return false;
	}else{
		return true;
	}
}

function checkIfAliasOwner($alias = null, $cid = null) {
	list($count) = mysql_fetch_array(mysql_query("Select count(*) as aliasCount From company_alias Where alias='$alias' And cid='$cid';"));
	
	if (intval($count)) {
		return true;
	}else{
		return false;
	}
}

function cleanArray ($array) {
	if (is_array($array)) {
		foreach ($array as $key => $val) {
			if (is_array($array[$key])){
				foreach ($array[$key] as $key2 => $val2) {
					if (!is_array($array[$key][$key2])){
						$array[$key][$key2] = mysql_real_escape_string($val2);
					}
				}
			}else{
				$array[$key] = mysql_real_escape_string($val);
			}
		}
	}
	
	return $array;
}

function stripSlashesFromArray ($array) {
	if (is_array($array)) {
		foreach ($array as $key => $val) {
			$array[$key] = stripslashes($val);
		}
	}
	
	return $array;
}

function createSessionVariablesFromArray ($array) {
	if (is_array($array)) {
		foreach ($array as $key => $val) {
			$_SESSION[$key] = $val;
		}
	}
	
	return;
}

function destroySessionVariablesFromArray ($array) {
	if (is_array($array)) {
		foreach ($array as $key => $val) {
			unset($_SESSION[$key]);
		}
	}
	
	return;
}

function createStateList($curState = null) {
	global $state_list;
	foreach ($state_list as $key => $val) { ?>
		<option value="<?= $key; ?>" <?= ($curState == $key) ? "selected" : "" ; ?>><?= $val; ?></option>
		
	<?php }
}

function createStateListString($curState = null) {
	global $state_list;
	$string_list = "";
	
	foreach ($state_list as $key => $val) {
		$string_list .= "<option value=\"".$key."\" ";
		$string_list .= ($curState == $key) ? "selected" : "" ;
		$string_list .= ">".$val."</option>";
	}
	
	return $string_list;
}

function getStateFullName($curState = null) {
	global $state_list;
	
	return $state_list[$curState];
}

function getStateAbbreviatedName($curState = null) {
	global $state_list;
	
	return array_search(strtoupper($curState), array_map('strtoupper', $state_list)) ? array_search(strtoupper($curState), array_map('strtoupper', $state_list)) : $curState;
}

function getCCTypeFromShort($ccType = null) {
	switch ($ccType) {
		case "V":
			return "Visa";
			break;
		case "M":
			return "Mastercard";
			break;
		case "A":
			return "American Express";
			break;
		case "DI":
			return "Discover";
			break;
	}
}

function createSICList($curSIC = null) {
	$sic_list = mysql_query("Select * From company_sic_codes Order By code;");
	
	while ($sic = mysql_fetch_assoc($sic_list)) { ?>
		<option value="<?= $sic['scid']; ?>" <?= ($curSIC == $sic['scid']) ? "selected" : "" ; ?>><?= $sic['code']; ?> - <?= $sic['name']; ?></option>
		
	<? }
}

function getSICFullName($curSIC = null) {
	list($name) = mysql_fetch_array(mysql_query("Select name From company_sic_codes Where scid='$curSIC';"));
	
	return $name;
}

function getProductCategories() {
	$query = "SELECT * FROM product_categories ORDER BY name";
	
	if (!($results = mysql_query($query))) {
		$error = array("title" => "Problem getting getProductCategories", "desc" => mysql_error()."<br/><br/>Query: ".$query);
		insertError($error);
		return false;
	}
	
	return $results;
}

function buildSearchProductQuery($display_start = 0, $display_count = 20, $search_term = "", $price_range_min = 0, $price_range_max = 0, $category = 0) {
	include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/doba_config.php");
	
	$strRequest = "
	<dce>
		<request>
			<authentication>
				<username>". $api_username ."</username>
				<password>". $api_password ."</password>
			</authentication>
			<retailer_id>". $api_retailer_id ."</retailer_id>
			<display_count>$display_count</display_count>
			<display_start>$display_start</display_start>";
	
	if ($category) {
		$strRequest .= "
			<category_id>$category</category_id>";
	}
	
	if ($search_term) {
		$strRequest .= "
			<search_term>$search_term</search_term>";
	}
	
	if ($price_range_min) {
		$strRequest .= "
			<price_range_min>$price_range_min</price_range_min>";
	}
	
	if ($price_range_max) {
		$strRequest .= "
			<price_range_max>$price_range_max</price_range_max>";
	}
	
	$strRequest .=	"
			<rollup_products>1</rollup_products>
			<filter>
				<name>f_hasImage</name>
				<ids>
					<id>1</id>
				</ids>
			</filter>";
	
	$strRequest .=	"
			<action>searchCatalog</action>
		</request>
	</dce>";
	
	//echo "<pre>".htmlentities($strRequest)."</pre>";
	
	return sendReceiveDobaData($strRequest, $api_url);
}

function sendOrderToDoba($itemsArray) {
	include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/doba_config.php");
	
	$strRequest = "
	<dce>
		<request>
			<authentication>
				<username>". $api_username ."</username>
				<password>". $api_password ."</password>
			</authentication>
			<retailer_id>". $api_retailer_id ."</retailer_id>
			<action>createOrder</action>
			<shipping_firstname>".$_SESSION['fname']."</shipping_firstname>
			<shipping_lastname>".$_SESSION['lname']."</shipping_lastname>
			<shipping_street>".$_SESSION['address1']." ".$_SESSION['address2']."</shipping_street>
			<shipping_city>".$_SESSION['city']."</shipping_city>
			<shipping_state>".$_SESSION['state']."</shipping_state>
			<shipping_postal>".$_SESSION['zip']."</shipping_postal>
			<shipping_country>US</shipping_country>
			<ip_address>".$_SERVER['REMOTE_ADDR']."</ip_address>
			<items>";
	
	foreach ($itemsArray as $item) {
		$strRequest .= "
				<item>
					<item_id>".$item['item_id']."</item_id>
					<quantity>".$item['qty']."</quantity>
				</item>";
	}
	
	$strRequest .=	"
			</items>
		</request>
	</dce>";
	
	//echo "<pre>".htmlentities($strRequest)."</pre>";
	
	return sendReceiveDobaData($strRequest, $api_url);
}

function buildProductQuery($products = 0) {
	include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/doba_config.php");
	
	$strRequest = "
	<dce>
		<request>
			<authentication>
				<username>". $api_username ."</username>
				<password>". $api_password ."</password>
			</authentication>
			<retailer_id>". $api_retailer_id ."</retailer_id>";
	
	if ($products) {
		$strRequest .= "
			<products>";
		if (is_array($products)) {
			foreach ($products as $product) {
				$strRequest .= 
				"<product>
					$product
				</product>";
			}
		}else{
			$strRequest .= 
				"<product>$products</product>";
		}
		$strRequest .= "
			</products>";
	}
	
	$strRequest .=	"
			<action>getProductInventory</action>
		</request>
	</dce>";
	
	//echo "<pre>".htmlentities($strRequest)."</pre>";
	
	return sendReceiveDobaData($strRequest, $api_url);
}

function sendReceiveDobaData($xml_data = null, $url = "https://sandbox.doba.com/api/20110301/xml_retailer_api.php") {
	//initialize a CURL session
	$connection = curl_init();
	//set the server we are using (could be Sandbox or Production server)
	curl_setopt($connection, CURLOPT_URL, $url);
	//stop CURL from verifying the peer's certificate
	curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
	//set method as POST
	curl_setopt($connection, CURLOPT_POST, 1);
	//set the XML body of the request
	curl_setopt($connection, CURLOPT_POSTFIELDS, $xml_data);
	//set it to return the transfer as a string from curl_exec
	curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
	//increase time-out as some calls can take a long time to respond with data
	set_time_limit(108000);
	//Send the Request
	$strResponse = curl_exec($connection);
	if(curl_errno($connection)) {
	    print 'Curl error: ' . curl_error($connection);
	} 
	//close the connection
	curl_close($connection);
	
	$regex = "/.*(?=\]\&gt;)\]\&gt;\n/s";
	
	$strResponse = preg_replace($regex, "", $strResponse);
	
	$oXmlParser = new xmlParser();
	$array = $oXmlParser->xml2array($strResponse);
	return $array;
}

function parseSearchCatalog($xml = null) {
	if ($xml) {
		$xml = new SimpleXMLElement($xml);
		
		$oXmlParser = new xmlParser();
		$array = $oXmlParser->xml2array($xml);
	}
}

function getAllAvailableProductsList($start = 0, $limit = 20, $term = null, $min_price = 0, $max_price = 0, $category = null) {
	if ($term) {
		$term = "And (name Like '%$term%' Or description Like '%$term%')";
	}else{
		$term = "";
	}
	
	if ($min_price) {
		$min_price = "And value > ".($min_price / 75)."";
	}else{
		$min_price = "";
	}
	
	if ($max_price) {
		$max_price = "And value < ".($max_price / 75)."";
	}else{
		$max_price = "";
	}
	
	if ($category) {
		$category = "And category = '$category'";
	}else{
		$category = "";
	}
	
	$query = "Select * From products Where active = 1 $term $min_price $max_price $category Limit $start, $limit;";
	$query2 = "Select Count(*) As totalCount From products Where active = 1 $term $min_price $max_price $category;";
	
	list($count) = mysql_fetch_array(mysql_query($query2));
	
	if (!($results = mysql_query($query))) {
		$error = array("title" => "Problem selecting product from getAllAvailableProductsList", "desc" => mysql_error()."<br/><br/>Query: ".$query);
		insertError($error);
		return false;
	}
	
	return array("count" => $count, "results" => $results);
}

function getProductInfoFromDatabase($pid = null) {
	if ($pid) {
		$query = "Select * From products Where pid='$pid' Limit 1;";
		
		if (!($results = mysql_fetch_assoc(mysql_query($query)))) {
			$error = array("title" => "Problem selecting product from getProductInfoFromDatabase", "desc" => mysql_error()."<br/><br/>Query: ".$query);
			insertError($error);
			return false;
		}
		
		return $results;
	}
	
	return false;
}

function facebookWallPost ($message, $appId, $appSecret, $page_id, $access_token) {
	require 'facebook/facebook.php';
 
	// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
		'appId'  => $appId,
		'secret' => $appSecret,
	));
	
	try {
		$args = array(
			'access_token'  => $access_token,
			'message'       => str_replace(array("\r\n", "\r", "\n", "\t","&nbsp;"),"",stripcslashes($message))
		);
		$post_id = $facebook->api("/$page_id/feed","post",$args);
	} catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
		
		$error = array("title" => "Could not post to facebook", "desc" => mysql_error()."<br/><br/>Error: ".mysql_real_escape_string($e));
		insertError($error);
		
		return false;
	}
	
	return true;
}

function facebookGetUserPages ($user_id) {
	$array = $facebook->api("/$user_id");
	
	/*
	$url = "https://graph.facebook.com/$user_id?access_token=".$_SESSION['extened_fb_token'];
	//echo $url;
	
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	
	$array = array();
	$array = json_decode((string)$data, true);
	*/
	
	return $array;
}

function getNumberOfDaysSince ($startDate) {
	$date = strtotime("$startDate");
	$dateMax = date("Y-m-d");
	
	$days = 0;
	while (date("Y-m-d", $date) < $dateMax) {
		$days++;
		$date += 24 * 3600;
	}
	
	return $days;
}

function getNumberOfDaysTill ($endDate) {
	$date = date("Y-m-d");
	$dateMax = strtotime("$endDate");
	
	$days = 0;
	while (date("Y-m-d", $date) < $dateMax) {
		$days++;
		$date += 24 * 3600;
	}
	
	return $days;
}

function getDatesOfDaysSince ($startDate) {
	$date = strtotime($startDate);
	$dateMax = date("Y-m-d");
	
	$days = array();
	while (date("Y-m-d", $date) <= $dateMax) {
		array_push($days, date("Y-m-d", $date));
		$date += 24 * 3600;
	}
	
	return $days;
}

function getDatesOfDaysTill ($endDate) {
	$date = strtotime(date("Y-m-d"));
	$dateMax = date("Y-m-d", strtotime("$endDate"));
	
	$days = array();
	while (date("Y-m-d", $date) <= $dateMax) {
		array_push($days, date("Y-m-d", $date));
		$date += 24 * 3600;
	}
	
	return $days;
}

function getNumberOfWeeksSince ($startDate) {
	$date = strtotime("$startDate next sunday");
	$dateMax = date("Y-m-d");
	
	$weeks = 0;
	while (date("Y-m-d", $date) < $dateMax) {
		$lastWeek = date('Y-m-d', $date);
		$weeks++;
		$date += 7 * 24 * 3600;
	}
	
	return array("weeks" => $weeks, "last_week" => $lastWeek);
}

function getNumberOfWeeksTill ($endDate) {
	$date = (int) date("W", strtotime("first day of ".date("Y-m")));
	$endDate = (int) date("w", strtotime("last day of ".date("Y-m", strtotime($endDate))));
	
	return count(range($date, $endDate));
}

function getDatesOfWeeksSince ($startDate) {
	$date = strtotime("$startDate last sunday");
	$dateMax = date("Y-m-d");
	
	$weeks = array();
	while (date("Y-m-d", $date) < $dateMax) {
		array_push($weeks, date("Y-m-d", $date));
		$date += 7 * 24 * 3600;
	}
	
	return $weeks;
}

function getDatesOfWeeksTill ($endDate) {
	$date = strtotime(date("Y-m-d") . " last sunday");
	$dateMax = date("Y-m-d", strtotime($endDate));
	
	$weeks = array();
	while (date("Y-m-d", $date) < $dateMax) {
		array_push($weeks, date("Y-m-d", $date));
		$date += 7 * 24 * 3600;
	}
	
	return $weeks;
}

function getNumberOfMonthsSince ($startDate) {
	$month1 = date("Y", strtotime($startDate)) + date("m", strtotime($startDate));
	$month2 = date("Y") + date("m");
	
	return ($month2 - $month1) + 1;
}

function getNumberOfMonthsTill ($endDate) {
	$month1 = date("Y") + date("m");
	$month2 = date("Y", strtotime($endDate)) + date("m", strtotime($endDate));
	
	return ($month2 - $month1) + 1;
}

function getDatesOfMonthsSince ($startDate) {
	$date = strtotime(date("Y-m", strtotime("$startDate"))."-01");
	$dateMax = date("Y-m-t");
	
	$months = array();
	while (date("Y-m-d", $date) < $dateMax) {
		$date = date("Y-m", $date);
		$date = strtotime($date."-01");
		
		array_push($months, date("Y-m-d", $date));
		//# of days in the current month
		$dim = date("t", $date);
		
		// one day times the number of days in a month
		$date += $dim * 24 * 3605;
	}
	
	return $months;
}

function getDatesOfMonthsTill ($endDate) {
	$date = strtotime(date("Y-m")."-01");
	$dateMax = date("Y-m-t", strtotime($endDate));
	
	$months = array();
	while (date("Y-m-d", $date) < $dateMax) {
		$date = date("Y-m", $date);
		$date = strtotime($date."-01");
		
		array_push($months, date("Y-m-d", $date));
		//# of days in the current month
		$dim = date("t", $date);
		
		// one day times the number of days in a month
		$date += $dim * 24 * 3605;
	}
	
	return $months;
}

function getNumberOfRepetitionsInMonth ($frequency) {
	switch ($frequency) {
		case "daily":
			return date("t");
			break;
		case "weekly":
			return ceil(date("t") / 7);
			break;
		case "monthly":
			return 1;
			break;
		default:
			return 1;
			break;
	}
}

function aasort (&$array, $key) {
	$sorter=array();
	$ret=array();
	reset($array);
	foreach ($array as $ii => $va) {
		$sorter[$ii]=$va[$key];
	}
	asort($sorter);
	foreach ($sorter as $ii => $va) {
		$ret[$ii]=$array[$ii];
	}
	$array=$ret;
}

function getShoutBox($cid = null) {
	if (!$cid) {
		$cid = getUserCID();
	}
	
	$query = "Select company_shoutbox.uid, company_shoutbox.copy, company_shoutbox.shout_date, user_meta.fname, user_meta.lname, user_meta.avatar
		  From company_shoutbox
		  Inner Join user_meta On (company_shoutbox.cid = user_meta.cid And company_shoutbox.uid = user_meta.uid)
		  Where company_shoutbox.cid='$cid' And company_shoutbox.active=1
		  Order By company_shoutbox.shout_date Desc
		  Limit 150;";
	
	
	if (!$results = mysql_query($query)) {
		$error = array("title" => "Problem getting getShoutBox", "desc" => mysql_error()."<br/><br/>Query: ".$query);
		insertError($error);
		return false;
	}
	
	return $results;
}

function postShoutOut($copy = null, $uid = null, $cid = null) {
	if (!$copy) return false;
	
	if (!$cid) {
		$cid = getUserCID();
	}
	if (!$uid) {
		$uid = $_SESSION['uid'];
	}
	
	$query = "Insert Into company_shoutbox (cid, uid, active, copy, shout_date) Values ('$cid', '$uid', '1', '$copy', now());";
	
	if (!mysql_query($query)) {
		$error = array("title" => "Problem Function: postShoutOut", "desc" => mysql_error()."<br/><br/>Query: ".$query);
		insertError($error);
		return false;
	}
	
	return true;
}

/*
 * PHP function to resize an image maintaining aspect ratio
 * http://salman-w.blogspot.com/2008/10/resize-images-using-phpgd-library.html
 *
 * Creates a resized (e.g. thumbnail, small, medium, large)
 * version of an image file and saves it as another file
 */

define('LOGO_IMAGE_MAX_WIDTH', 179);
define('LOGO_IMAGE_MAX_HEIGHT', 89);

function generate_image_logo($source_image_path, $thumbnail_image_path)
{
    list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
    switch ($source_image_type) {
        case IMAGETYPE_GIF:
            $source_gd_image = imagecreatefromgif($source_image_path);
            break;
        case IMAGETYPE_JPEG:
            $source_gd_image = imagecreatefromjpeg($source_image_path);
            break;
        case IMAGETYPE_PNG:
            $source_gd_image = imagecreatefrompng($source_image_path);
            break;
    }
    if ($source_gd_image === false) {
        return false;
    }
    $source_aspect_ratio = $source_image_width / $source_image_height;
    $thumbnail_aspect_ratio = LOGO_IMAGE_MAX_WIDTH / LOGO_IMAGE_MAX_HEIGHT;
    if ($source_image_width <= LOGO_IMAGE_MAX_WIDTH && $source_image_height <= LOGO_IMAGE_MAX_HEIGHT) {
        $thumbnail_image_width = $source_image_width;
        $thumbnail_image_height = $source_image_height;
    } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
        $thumbnail_image_width = (int) (LOGO_IMAGE_MAX_HEIGHT * $source_aspect_ratio);
        $thumbnail_image_height = LOGO_IMAGE_MAX_HEIGHT;
    } else {
        $thumbnail_image_width = LOGO_IMAGE_MAX_WIDTH;
        $thumbnail_image_height = (int) (LOGO_IMAGE_MAX_WIDTH / $source_aspect_ratio);
    }
    $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
    imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
    imagejpeg($thumbnail_gd_image, $thumbnail_image_path, 90);
    imagedestroy($source_gd_image);
    imagedestroy($thumbnail_gd_image);
    return true;
}

define('LARGE_IMAGE_MAX_WIDTH', 606);
define('LARGE_IMAGE_MAX_HEIGHT', 222);

function generate_image_large($source_image_path, $thumbnail_image_path)
{
    list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
    switch ($source_image_type) {
        case IMAGETYPE_GIF:
            $source_gd_image = imagecreatefromgif($source_image_path);
            break;
        case IMAGETYPE_JPEG:
            $source_gd_image = imagecreatefromjpeg($source_image_path);
            break;
        case IMAGETYPE_PNG:
            $source_gd_image = imagecreatefrompng($source_image_path);
            break;
    }
    if ($source_gd_image === false) {
        return false;
    }
    $source_aspect_ratio = $source_image_width / $source_image_height;
    $thumbnail_aspect_ratio = LARGE_IMAGE_MAX_WIDTH / LARGE_IMAGE_MAX_HEIGHT;
    if ($source_image_width <= LARGE_IMAGE_MAX_WIDTH && $source_image_height <= LARGE_IMAGE_MAX_HEIGHT) {
        $thumbnail_image_width = $source_image_width;
        $thumbnail_image_height = $source_image_height;
    } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
        $thumbnail_image_width = (int) (LARGE_IMAGE_MAX_HEIGHT * $source_aspect_ratio);
        $thumbnail_image_height = LARGE_IMAGE_MAX_HEIGHT;
    } else {
        $thumbnail_image_width = LARGE_IMAGE_MAX_WIDTH;
        $thumbnail_image_height = (int) (LARGE_IMAGE_MAX_WIDTH / $source_aspect_ratio);
    }
    $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
    imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
    imagejpeg($thumbnail_gd_image, $thumbnail_image_path, 90);
    imagedestroy($source_gd_image);
    imagedestroy($thumbnail_gd_image);
    return true;
}

define('CITY_IMAGE_MAX_WIDTH', 950);
define('CITY_IMAGE_MAX_HEIGHT', 417);

function generate_image_city($source_image_path, $thumbnail_image_path)
{
    list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
    switch ($source_image_type) {
        case IMAGETYPE_GIF:
            $source_gd_image = imagecreatefromgif($source_image_path);
            break;
        case IMAGETYPE_JPEG:
            $source_gd_image = imagecreatefromjpeg($source_image_path);
            break;
        case IMAGETYPE_PNG:
            $source_gd_image = imagecreatefrompng($source_image_path);
            break;
    }
    if ($source_gd_image === false) {
        return false;
    }
    $source_aspect_ratio = $source_image_width / $source_image_height;
    $thumbnail_aspect_ratio = CITY_IMAGE_MAX_WIDTH / CITY_IMAGE_MAX_HEIGHT;
    if ($source_image_width <= CITY_IMAGE_MAX_WIDTH && $source_image_height <= CITY_IMAGE_MAX_HEIGHT) {
        $thumbnail_image_width = $source_image_width;
        $thumbnail_image_height = $source_image_height;
    } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
        $thumbnail_image_width = (int) (CITY_IMAGE_MAX_HEIGHT * $source_aspect_ratio);
        $thumbnail_image_height = CITY_IMAGE_MAX_HEIGHT;
    } else {
        $thumbnail_image_width = CITY_IMAGE_MAX_WIDTH;
        $thumbnail_image_height = (int) (CITY_IMAGE_MAX_WIDTH / $source_aspect_ratio);
    }
    $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
    imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
    imagejpeg($thumbnail_gd_image, $thumbnail_image_path, 90);
    imagedestroy($source_gd_image);
    imagedestroy($thumbnail_gd_image);
    return true;
}

function get_user_location () {
	$key = "0b67fde18b072df9263a66f5526500591ee4d374ac43ef5884a8b31c9840dd1f";
	$ip = $_SERVER['REMOTE_ADDR'];
	$url = "http://api.ipinfodb.com/v3/ip-city/?key=$key&ip=$ip&format=json";
	$cont = file_get_contents($url);
	$data = json_decode($cont , true);
	$result = false;
	
	if(strlen($data['latitude'])) {
		$result = array(
			'ip' => $data['ipAddress'] ,
			'country_code' => $data['countryCode'] ,
			'country_name' => $data['countryName'] ,
			'region_name' => $data['regionName'] ,
			'city' => $data['cityName'] ,
			'zip_code' => $data['zipCode'] ,
			'latitude' => $data['latitude'] ,
			'longitude' => $data['longitude'] ,
			'time_zone' => $data['timeZone'] ,
		);
	}
	
	//print_r($result);
	
	return $result; 
}

function get_random_location () {
	$query = "Select ZipCode, Longitude, Latitude From zipCodes  Order By rand() Limit 1;";
	
	if($results = mysql_fetch_assoc(mysql_query($query))) {
		$result = array(
			'zip_code' => $results['ZipCode'] ,
			'latitude' => $results['Latitude'] ,
			'longitude' => $results['Longitude']
		);
	}
	
	//print_r($results);
	
	return $result; 
}
?>