<?php
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

function getLoadTime($start) {
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$total_time = round(($finish - $start), 4);
	echo 'Page generated in '.$total_time.' seconds.';
}
?>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
$_SESSION['location']['zip_code'] = 45014;
if (stristr($_SERVER['REQUEST_URI'], '/services/') && !stristr($_SERVER['REQUEST_URI'], '/services/?')) {
	//echo $_SERVER['REQUEST_URI'];
	$path = explode('/', $_SERVER['REQUEST_URI']);
	//print_r($path);
	$category = str_replace('[and]', '/', urldecode($path[2]));
	if (count($path) == 5) {
		$subcat = str_replace('[and]', '/', urldecode($path[3]));
	}
	$zip = urldecode($path[count($path) - 1]);
	
	if ($zip) {
		$_SESSION['zip'] = $zip;
	}
	
	if (strlen($subcat)) {
		$query = "Select categories.id
					From categories
					Inner Join mcat On (categories.mcat_id = mcat.mcat_id)
					Where categories.safe_name = '$subcat'
					And mcat.safe_name = '$category'
					Limit 1;";
		list($_REQUEST['cat']) = mysql_fetch_array(mysql_query($query));
		$_REQUEST['cat'] = $_REQUEST['cat'];
	}elseif (strlen($category)){
		$query = "Select mcat_id From mcat Where safe_name = '$category' Limit 1;";
		list($_REQUEST['mcat']) = mysql_fetch_array(mysql_query($query));
		$_REQUEST['mcat'] = $_REQUEST['mcat'];
	}
}

if ($_REQUEST['r'] || stristr($_SERVER['REQUEST_URI'], '/services/?r=1')) {
    unset($_SESSION['zip']);
}

if (!$_SESSION['zip']) {
    $_SESSION['zip'] = $_SESSION['location']['zip_code'];
}

if (isset($_REQUEST['zip'])) {
    $_SESSION['zip'] = mysql_real_escape_string($_REQUEST['zip']);
}

if (isset($_SESSION['zip'])) {
	
	$nfound = "";
	
	if (isset($_REQUEST['cat']) && intval($_REQUEST['cat'])){
		$cat = intval($_REQUEST['cat']);
		
		$result = mysql_fetch_assoc(mysql_query("SELECT zipCodes.City as zipCity, zipCodes.State as zipState, zipCodes.Latitude, zipCodes.Longitude, categories.name as category_name
												FROM zipCodes, categories
												Where zipCodes.ZipCode = '".$_SESSION['zip']."' And categories.id = '$cat'"));
		
		if (is_array($result)) {
			extract(stripSlashesFromArray($result));
			$nfound = $_SESSION['zip'];
		} else {
			$_SESSION['zip'] = $_SESSION['location']['zip_code'];
			$result = mysql_fetch_assoc(mysql_query("SELECT zipCodes.City as zipCity, zipCodes.State as zipState, zipCodes.Latitude, zipCodes.Longitude, categories.name as category_name
												FROM zipCodes, categories
												Where zipCodes.ZipCode = '".$_SESSION['zip']."' And categories.id = '$cat'"));
			if (is_array($result)) {
				extract(stripSlashesFromArray($result));
			} 
		}
		
		$header_array = array('title' => ucwords(strtolower($zipCity)).', '.ucwords(strtolower($zipState)).' '.ucwords(strtolower($category_name)).' | Business Directory',
							  'description' => '',
							  'keywords' => '');
	}elseif (isset($_REQUEST['mcat']) && intval($_REQUEST['mcat'])){
		$mcat = intval($_REQUEST['mcat']);
		
		$result = mysql_fetch_assoc(mysql_query("SELECT zipCodes.City as zipCity, zipCodes.State as zipState, zipCodes.Latitude, zipCodes.Longitude, mcat.mcat_name as category_name
												FROM zipCodes, mcat
												Where zipCodes.ZipCode = '".$_SESSION['zip']."' And mcat.mcat_id = '$mcat'"));	
		if (is_array($result)) {
			extract(stripSlashesFromArray($result));
			$nfound = $_SESSION['zip'];
		} else {
			$_SESSION['zip'] = $_SESSION['location']['zip_code'];
			$result = mysql_fetch_assoc(mysql_query("SELECT zipCodes.City as zipCity, zipCodes.State as zipState, zipCodes.Latitude, zipCodes.Longitude, mcat.mcat_name as category_name
												FROM zipCodes, mcat
												Where zipCodes.ZipCode = '".$_SESSION['zip']."' And mcat.mcat_id = '$mcat'"));
			if (is_array($result)) {
				extract(stripSlashesFromArray($result));
			} 
		}
		
		$header_array = array('title' => ucwords(strtolower($zipCity)).', '.ucwords(strtolower($zipState)).' '.ucwords(strtolower($category_name)).' | Business Directory',
							  'description' => '',
							  'keywords' => '');
	}else{
		$result = mysql_fetch_assoc(mysql_query("SELECT zipCodes.City as zipCity, zipCodes.State as zipState, zipCodes.Latitude, zipCodes.Longitude
												FROM zipCodes
												Where zipCodes.ZipCode = '".$_SESSION['zip']."'"));
		if (is_array($result)) {
			extract(stripSlashesFromArray($result));
			$nfound = $_SESSION['zip'];
		} else {
			$nfound  = 'No Matching for '. $_SESSION['zip'] . ', showing ' .$_SESSION['location']['zip_code'];
			//$_SESSION['zip'] = $nfound;
			$result = mysql_fetch_assoc(mysql_query("SELECT zipCodes.City as zipCity, zipCodes.State as zipState, zipCodes.Latitude, zipCodes.Longitude
												FROM zipCodes
												Where zipCodes.ZipCode = '".$_SESSION['location']['zip_code']."'"));
			if (is_array($result)) {
				extract(stripSlashesFromArray($result));
			} 
		}
		
		$header_array = array('title' => ucwords(strtolower($zipCity)).', '.ucwords(strtolower($zipState)).' Business Directory',
							  'description' => '',
							  'keywords' => '');
	}
}else{
	$header_array = array('title' => 'Business Directory',
						  'description' => '',
						  'keywords' => '');
}



if (isset($_REQUEST['cat']) && strlen($_REQUEST['cat']) > 0) {
	$cat_id = intval($_REQUEST['cat']);
	mysql_query("Update categories Set views = (views + 1) Where id = '$cat_id'");
	list($mcat_id) = mysql_fetch_array(mysql_query("Select mcat_id From categories Where id = '$cat_id' Limit 1;"));
}else if (isset($_REQUEST['mcat']) && strlen($_REQUEST['mcat']) > 0) {
	$mcat_id = intval($_REQUEST['mcat']);
	mysql_query("Update mcat Set views = (views + 1) Where mcat_id = '$mcat_id'");
}
if(isset($path[3])) {
	$sq = "";
	if(isset($cat_id)) {
		$sq = "AND category_id = ".$cat_id;
	} else {
		$sq = "AND safe_name = '".$path[2]."'";
	}
	$sql = "SELECT meta_desc,meta_key FROM business_meta
	WHERE listZip = '".$path[3]."' ".$sq." LIMIT 1";

	$res = mysql_query($sql);
	$meta = mysql_fetch_assoc($res);
	$header_array['description'] = $meta['meta_desc'];
	$header_array['keywords'] = $meta['meta_key'];
}

list($mcatname, $mcat_safe_name) = mysql_fetch_array(mysql_query("SELECT mcat_name, safe_name FROM mcat WHERE mcat_id='$mcat_id' Limit 1;"));
list($catname, $cat_safe_name) = mysql_fetch_array(mysql_query("SELECT name, safe_name FROM categories WHERE id='$cat_id' Limit 1;"));

$zipResults = false;

if (isset($_SESSION['zip'])) {
	$zipResult = mysql_fetch_array(mysql_query("SELECT latitude,longitude FROM zipCodes WHERE zipCode='".mysql_real_escape_string($_SESSION['zip'])."' Limit 1"));
	
	if (!is_array($zipResult)) {
		$_SESSION['zip'] = $_SESSION['location']['zip_code'];
		$zipResult = mysql_fetch_array(mysql_query("SELECT latitude,longitude FROM zipCodes WHERE zipCode='".mysql_real_escape_string($_SESSION['zip'])."' Limit 1"));		
	}
	
	
	$latitude = $zipResult['latitude'];
	$longitude = $zipResult['longitude'];
	$radius = 50;
	
	$lon1 = $longitude - $radius / abs(cos(deg2rad($latitude))*69);
	$lon2 = $longitude + $radius / abs(cos(deg2rad($latitude))*69);
	$lat1 = $latitude - ($radius/69);
	$lat2 = $latitude + ($radius/69);
	
	if (strlen($latitude) && strlen($longitude)){
		if (isset($cat_id)) {
			$query1 = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState, zipCodes.longitude, zipCodes.latitude,
				(3956 * 2 * ASIN ( SQRT (POWER(SIN((zipCodes.latitude - $latitude)*pi()/180 / 2),2) + COS(zipCodes.latitude* pi()/180) * COS($latitude *pi()/180) * POWER(SIN((zipCodes.longitude - $longitude) *pi()/180 / 2), 2) ) )) as distance
				FROM business
				INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode)
				WHERE business.active = 1
				And (3958*3.1415926*sqrt((zipCodes.latitude-$latitude)*(zipCodes.latitude-$latitude) + cos(zipCodes.latitude/57.29578)*cos($latitude/57.29578)*(zipCodes.longitude-$longitude)*(zipCodes.longitude-$longitude))/180) <= '$radius'
				And zipCodes.longitude between $lon1 and $lon2 and zipCodes.latitude between $lat1 and $lat2
				And featured = 'yes' AND category_id = '$cat_id' GROUP BY business.id ORDER BY distance LIMIT 3";
			
			$query2 = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState, zipCodes.longitude,zipCodes.latitude, mcat.mcat_id, mcat.mcat_name, 
				(3956 * 2 * ASIN ( SQRT (POWER(SIN((zipCodes.latitude - $latitude)*pi()/180 / 2),2) + COS(zipCodes.latitude* pi()/180) * COS($latitude *pi()/180) * POWER(SIN((zipCodes.longitude - $longitude) *pi()/180 / 2), 2) ) )) as distance
				FROM business
				INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode)
				INNER JOIN categories ON (business.category_id = categories.id)
				INNER JOIN mcat ON (categories.mcat_id = mcat.mcat_id)
				WHERE business.active = 1
				And (3958*3.1415926*sqrt((zipCodes.latitude-$latitude)*(zipCodes.latitude-$latitude) + cos(zipCodes.latitude/57.29578)*cos($latitude/57.29578)*(zipCodes.longitude-$longitude)*(zipCodes.longitude-$longitude))/180) <= '$radius'
				And zipCodes.longitude between $lon1 and $lon2 and zipCodes.latitude between $lat1 and $lat2 
				And category_id = '$cat_id' GROUP BY id ORDER BY distance LIMIT 7";
		}else if (isset($mcat_id)) {
			$query1 = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState,zipCodes.longitude,zipCodes.latitude,
				(3956 * 2 * ASIN ( SQRT (POWER(SIN((zipCodes.latitude - $latitude)*pi()/180 / 2),2) + COS(zipCodes.latitude* pi()/180) * COS($latitude *pi()/180) * POWER(SIN((zipCodes.longitude - $longitude) *pi()/180 / 2), 2) ) )) as distance
				FROM business
				INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode)
				INNER JOIN categories ON (business.category_id = categories.id)
				WHERE business.active = 1
				And (3958*3.1415926*sqrt((zipCodes.latitude-$latitude)*(zipCodes.latitude-$latitude) + cos(zipCodes.latitude/57.29578)*cos($latitude/57.29578)*(zipCodes.longitude-$longitude)*(zipCodes.longitude-$longitude))/180) <= '$radius'
				And zipCodes.longitude between $lon1 and $lon2 and zipCodes.latitude between $lat1 and $lat2 
				And featured = 'yes' AND categories.mcat_id = '$mcat_id' GROUP BY business.id ORDER BY distance LIMIT 3";
			
			$query2 = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState,zipCodes.longitude,zipCodes.latitude,
				(3956 * 2 * ASIN ( SQRT (POWER(SIN((zipCodes.latitude - $latitude)*pi()/180 / 2),2) + COS(zipCodes.latitude* pi()/180) * COS($latitude *pi()/180) * POWER(SIN((zipCodes.longitude - $longitude) *pi()/180 / 2), 2) ) )) as distance
				FROM business
				INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode) 
				INNER JOIN categories ON (business.category_id = categories.id)
				WHERE business.active = 1
				And (3958*3.1415926*sqrt((zipCodes.latitude-$latitude)*(zipCodes.latitude-$latitude) + cos(zipCodes.latitude/57.29578)*cos($latitude/57.29578)*(zipCodes.longitude-$longitude)*(zipCodes.longitude-$longitude))/180) <= '$radius'
				And zipCodes.longitude between $lon1 and $lon2 and zipCodes.latitude between $lat1 and $lat2 
				And categories.mcat_id = '$mcat_id' GROUP BY id ORDER BY distance LIMIT 7";
		}else{
			$frontFeatured = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState,zipCodes.longitude,zipCodes.latitude,
				(3956 * 2 * ASIN ( SQRT (POWER(SIN((zipCodes.latitude - $latitude)*pi()/180 / 2),2) + COS(zipCodes.latitude* pi()/180) * COS($latitude *pi()/180) * POWER(SIN((zipCodes.longitude - $longitude) *pi()/180 / 2), 2) ) )) as distance
				FROM business
				INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode)
				WHERE business.active = 1
				And (3958*3.1415926*sqrt((zipCodes.latitude-$latitude)*(zipCodes.latitude-$latitude) + cos(zipCodes.latitude/57.29578)*cos($latitude/57.29578)*(zipCodes.longitude-$longitude)*(zipCodes.longitude-$longitude))/180) <= '$radius'
				And zipCodes.longitude between $lon1 and $lon2 and zipCodes.latitude between $lat1 and $lat2 
				And featured = 'yes' And topFeatured = '1' GROUP BY id, category_id ORDER BY distance LIMIT 1";
			
			$firstFeatured = mysql_fetch_assoc(mysql_query($frontFeatured));
			//echo mysql_error();
			
			$query1 = "";
			
			$query1 = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState,zipCodes.longitude,zipCodes.latitude,
				(3956 * 2 * ASIN ( SQRT (POWER(SIN((zipCodes.latitude - $latitude)*pi()/180 / 2),2) + COS(zipCodes.latitude* pi()/180) * COS($latitude *pi()/180) * POWER(SIN((zipCodes.longitude - $longitude) *pi()/180 / 2), 2) ) )) as distance
				FROM business
				INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode)
				WHERE business.active = 1
				And (3958*3.1415926*sqrt((zipCodes.latitude-$latitude)*(zipCodes.latitude-$latitude) + cos(zipCodes.latitude/57.29578)*cos($latitude/57.29578)*(zipCodes.longitude-$longitude)*(zipCodes.longitude-$longitude))/180) <= '$radius'
				And zipCodes.longitude between $lon1 and $lon2 and zipCodes.latitude between $lat1 and $lat2 
				And featured = 'yes'
				And business.id != '$firstFeatured[id]'
				GROUP BY id, category_id ORDER BY distance
				LIMIT 2";
			
			/*$query1 = "SELECT business.*,zipCodes.longitude,zipCodes.latitude,
				((3958*3.1415926*sqrt((zipCodes.latitude-$latitude)*(zipCodes.latitude-$latitude) + cos(zipCodes.latitude/57.29578)*cos($latitude/57.29578)*(zipCodes.longitude-$longitude)*(zipCodes.longitude-$longitude))/180))
				as distance FROM business INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode)
				WHERE (3958*3.1415926*sqrt((zipCodes.latitude-$latitude)*(zipCodes.latitude-$latitude) + cos(zipCodes.latitude/57.29578)*cos($latitude/57.29578)*(zipCodes.longitude-$longitude)*(zipCodes.longitude-$longitude))/180) <= '$radius'
				And featured = 'yes' And business.id != '$firstFeatured[id]' GROUP BY id, category_id ORDER BY distance LIMIT 2";*/
			
			$query2 = "Select * From (SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState,zipCodes.longitude,zipCodes.latitude,
				(3956 * 2 * ASIN ( SQRT (POWER(SIN((zipCodes.latitude - $latitude)*pi()/180 / 2),2) + COS(zipCodes.latitude* pi()/180) * COS($latitude *pi()/180) * POWER(SIN((zipCodes.longitude - $longitude) *pi()/180 / 2), 2) ) )) as distance
				FROM business
				INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode)
				Where business.active = 1
				And (3958*3.1415926*sqrt((zipCodes.latitude-$latitude)*(zipCodes.latitude-$latitude) + cos(zipCodes.latitude/57.29578)*cos($latitude/57.29578)*(zipCodes.longitude-$longitude)*(zipCodes.longitude-$longitude))/180) <= '$radius'
				And zipCodes.longitude between $lon1 and $lon2 and zipCodes.latitude between $lat1 and $lat2
				GROUP BY business.id
				ORDER BY distance) As temp
				Group By category_id
				ORDER BY distance LIMIT 18";
		}
	}else{
		$query1 = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState FROM business INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode) Where business.featured = 'yes' And business.topFeatured = '1' And business.active = 1 Group By business.category_id Order By rand() Limit 3";
		
		$query2 = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState FROM business INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode) Where business.active = 1 Group By business.category_id Order By rand() LIMIT 18";
	}
}else{
	if (isset($cat_id)) {
		$query1 = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState FROM business INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode) Where category_id = $cat_id And featured = 'yes' Order By rand() Limit 3";
		
		$query2 = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState, mcat.mcat_id, mcat.mcat_name FROM business
			INNER JOIN categories ON (business.category_id = categories.id)
			INNER JOIN mcat ON (categories.mcat_id = mcat.mcat_id)
			INNER JOIN zipCodes ON (business.listZip = zipCodes.ZipCode)
			Where category_id = $cat_id And business.active = 1 Order By rand() Limit 7";
	}else if (isset($mcat_id)) {
		$query1 = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState FROM business 
			INNER JOIN categories ON (business.category_id = categories.id)
			INNER JOIN zipCodes ON (business.listZip = zipCodes.ZipCode)
			Where categories.mcat_id = '$mcat_id' And featured = '1' And business.active = 1 Order By rand() Limit 3";
		
		$query2 = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState FROM business
			INNER JOIN categories ON (business.category_id = categories.id)
			INNER JOIN zipCodes ON (business.listZip = zipCodes.ZipCode)
			Where categories.mcat_id = '$mcat_id' And business.active = 1 Order By rand() Limit 18";
	}else{
		$query1 = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState FROM business INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode) Where business.featured = 'yes' And business.topFeatured = '1' And business.active = 1 Group By business.category_id Order By rand() Limit 3";
		
		$query2 = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState FROM business INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode) Where business.active = 1 Group By business.category_id Order By rand() LIMIT 18";
	}
}

//echo $query1;
//echo "<br/><br/>";
//echo $query2;

//var_dump(get_defined_vars());
//echo $mcat_id."<br/><br/>".$cat_id."<br/><br/>".$frontFeatured."<br/><br/>".$query1."<br/><br/>".$query2;
//die();

include_once("includes/header.php");
?>
	<div id="main">
		<ul class="breadcrumbs">
			<li><a href="/services">View All</a></li>
			<li><a href="/services/<?php echo urlencode(str_replace('/', '[and]', $mcat_safe_name)); ?>/<?php echo $_SESSION['zip']; ?>"><?php echo strtoupper($mcatname); ?></a></li>
			<?php if ($catname) {?>
				<li class="active"><?php echo strtoupper($catname); ?></li>
			<?php } ?>
		</ul>
		<div class="twocolumns">
			<div id="content">
				<div class="head">
					<h1>
					    <?php if ($catname) {
							echo strtoupper($catname);
							echo ($_SESSION['zip']) ? " - ".$_SESSION['zip'] : $nfound;
					    }else if ($mcatname) {
							echo strtoupper($mcatname);
							echo ($_SESSION['zip']) ? " - ".$_SESSION['zip'] : $nfound;
					    }else if ($_SESSION['zip']) {
							echo $nfound;
					    }?>
					    <?php if ($_SESSION['zip']) {?>
							&emsp;&emsp;&emsp;<a href="/services/?<?php echo $mcat_id ? "mcat=".$mcat_id."&": "" ; ?><?php echo $cat_id ? "cat=".$cat_id."&": "" ; ?>r=1">Reset</a>
					    <?php } ?>
					</h1>
                    <div style="clear:both"></div>
					<div class="form-search">
						<div class="form-holder">
							<strong>SEARCH BY ZIP CODE:</strong>
						</div>
						<div class="box">
							<div class="box-holder">
								<form action="/services/" method="post">
									<fieldset>
										<label for="zip">ENTER YOUR ZIP CODE:</label>
										<div class="text">
											<input type="text" id="zip" name="zip" />
											<input type="hidden" name="mcat" value="<?php echo $mcat_id; ?>" />
											<input type="hidden" name="cat" value="<?php echo $cat_id; ?>" />
										</div>
										<input type="submit" value="SEARCH" />
									</fieldset>
								</form>
							</div>
						</div>
					</div>
                    
                    <div class="ui-widget cat-search" style="display:inline-block;padding:10px 0">
                        <a id="categorylinker">Search Category</a>
                        <form action="/services/" method="post" class="catform" id="categoryform">
                            <h5>Search Category</h5>
                            <input id="categorysearch" name="categorysearch" placeholder="" type="text">
                            <input id="catsearchbtn" name="catsearchbtn" value="Search" type="submit">
                        </form>
                    </div>
                    <div style="clear:both"></div>
				</div>
				
				<?php $results = mysql_query($query1);
				//echo mysql_error();
				
				if ($firstFeatured || mysql_num_rows($results)) { ?>
					<div class="featured-business">
						<div class="holder">
							<div class="frame">
								<div class="featured-content">
									<h2>FEATURED BUSINESSES</h2>
									<div class="slideshow">
										<div class="mask">
											<div class="slideset">
												<?php if ($firstFeatured) {
													extract($firstFeatured); ?>
													<div class="slide">
														<strong class="subheading">
															<a href="/c/<?php echo urlencode(stripslashes($safe_name)); ?>/<?php echo urlencode($zipCity); ?>/<?php echo urlencode($zipState); ?>">
																<?php echo stripslashes($name); ?>
															</a>
														</strong>
														<?php if ($large_image) {?>
															<img src="/images/businesses/<?php echo $large_image; ?>" width="605" height="222" alt="image description" class="aligncenter" />
														<?php } ?>
														<div class="block">
															<div class="info">
																<?php if (!empty($logo) && file_exists("images/businesses/$logo")) {?>
																    <div class="partners-logo">
																	<img src="/images/businesses/<?php echo $logo; ?>" alt="business logo" />
																    </div>
																<?php } ?>
																<address>
																	<div itemscope itemtype="http://schema.org/LocalBusiness">
																		<span itemprop="name"><?php echo stripslashes($name); ?></span>
																		<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
																			<span itemprop="streetAddress"><?php echo $address; ?></span>
																			<?php echo $address ? '<br/>' : ''; ?>
																			<span itemprop="addressLocality"><?php echo $city; ?></span><?php echo ($city && $state) ? ", " : "" ; ?>
																			<span itemprop="addressRegion"><?php echo $state; ?></span>
																			<span itemprop="postalCode"><?php echo $zip; ?></span>
																		</div>
																		<?php echo ($phone) ? "P: " : "" ; ?><span itemprop="telephone"><?php echo $phone; ?></span>
																		<br/>
																		<?php echo ($alt_phone) ? "Alt P: " : "" ; ?><span itemprop="telephone"><?php echo $alt_phone; ?></span>
																	</div>
																</address>
																<?php if ($website) {?>
																	<a target="_blank" rel="nofollow" href="<?php echo (substr($website, 0, 4) == "http") ? $website : "http://".$website ; ?>" class="link">Visit Website</a>
																<?}elseif ($facebook) {?>
																	<a target="_blank" rel="nofollow" href="<?php echo (substr($facebook, 0, 4) == "http") ? $facebook : "https://".$facebook ; ?>" class="link">Visit Facebook Page</a>
																<?php } ?>
															</div>
															<div class="area">
																<p><?php echo $description; ?></p>
															</div>
															
															<?php if ($facebook || $twitter || $instagram) { ?>
                                                                <div class="social-links">
																	<?php if ($facebook) {?>
																		<a target="_blank" rel="nofollow" href="<?php echo (substr($facebook, 0, 4) == 'http') ? $facebook : 'https://'.$facebook ; ?>" class=" facebook link">Visit Facebook Page</a>
																	<?php } ?>
																	<?php if ($twitter) {?>
																		<a target="_blank" rel="nofollow" href="<?php echo (substr($twitter, 0, 4) == 'http') ? $twitter : 'http://'.$twitter ; ?>" class=" twitter link">Visit Twitter Page</a>
																	<?php } ?>
																	<?php if ($instagram) {?>
																		<a target="_blank" rel="nofollow" href="<?php echo (substr($instagram, 0, 4) == 'http') ? $instagram : 'http://'.$instagram ; ?>" class="instagram link">Visit Instagram Page</a>
																	<?php } ?>
																</div>
															<?php }?>
														</div>
													</div>
												<?php }
												
												while ($business = mysql_fetch_assoc($results)) {
													extract($business); ?>
													<div class="slide">
														<strong class="subheading">
															<a href="/c/<?php echo urlencode(stripslashes($safe_name)); ?>/<?php echo urlencode($zipCity); ?>/<?php echo urlencode($zipState); ?>">
																<?php echo stripslashes($name); ?>
															</a>
														</strong>
														<?php if ($large_image) {?>
															<img src="/images/businesses/<?php echo $large_image; ?>" width="605" height="222" alt="image description" class="aligncenter" />
														<?php } ?>
														<div class="block">
															<div class="info">
																<?php if (!empty($logo) && file_exists("images/businesses/$logo")) {?>
																    <div class="partners-logo">
																	<img src="/images/businesses/<?php echo $logo; ?>" alt="business logo" />
																    </div>
																<?php } ?>
																<address>
																	<div itemscope itemtype="http://schema.org/LocalBusiness">
																		<span itemprop="name"><?php echo stripslashes($name); ?></span>
																		<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
																			<span itemprop="streetAddress"><?php echo $address; ?></span>
																			<?php echo $address ? '<br/>' : ''; ?>
																			<span itemprop="addressLocality"><?php echo $city; ?></span><?php echo ($city && $state) ? ", " : "" ; ?>
																			<span itemprop="addressRegion"><?php echo $state; ?></span>
																			<span itemprop="postalCode"><?php echo $zip; ?></span>
																		</div>
																		<?php echo ($phone) ? "P: " : "" ; ?><span itemprop="telephone"><?php echo $phone; ?></span>
																		<br/>
																		<?php echo ($alt_phone) ? "Alt P: " : "" ; ?><span itemprop="telephone"><?php echo $alt_phone; ?></span>
																	</div>
																</address>
																<?php if ($website) {?>
																	<a target="_blank" rel="nofollow" href="<?php echo (substr($website, 0, 4) == "http") ? $website : "http://".$website ; ?>" class="link">Visit Website</a>
																<?}elseif ($facebook) {?>
																	<a target="_blank" rel="nofollow" href="<?php echo (substr($facebook, 0, 4) == "http") ? $facebook : "https://".$facebook ; ?>" class="link">Visit Facebook Page</a>
																<?php } ?>
															</div>
															<div class="area">
																<p><?php echo $description; ?></p>
															</div>
															
															<?php if ($facebook || $twitter || $instagram) { ?>
																<div class="social-links">
																	<?php if ($facebook) {?>
																		<a target="_blank" rel="nofollow" href="<?php echo (substr($facebook, 0, 4) == 'http') ? $facebook : 'https://'.$facebook ; ?>" class=" facebook link">Visit Facebook Page</a>
																	<?php } ?>
																	<?php if ($twitter) {?>
																		<a target="_blank" rel="nofollow" href="<?php echo (substr($twitter, 0, 4) == 'http') ? $twitter : 'http://'.$twitter ; ?>" class=" twitter link">Visit Twitter Page</a>
																	<?php } ?>
																	<?php if ($instagram) {?>
																		<a target="_blank" rel="nofollow" href="<?php echo (substr($instagram, 0, 4) == 'http') ? $instagram : 'http://'.$instagram ; ?>" class="instagram link">Visit Instagram Page</a>
																	<?php } ?>
																</div>
															<?php }?>
														</div>
													</div>
												<?php } ?>
											</div>
										</div>
										<div class="switcher">
											<div class="switcher-holder">
												<a href="#" class="btn-prev">previous</a>
												<a href="#" class="btn-next">next</a>
												<strong class="slider-counter"><span class="cur-slide">1</span> of <span class="all-slide">10</span></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php }?>
				<?php $results = mysql_query($query2);
				//echo mysql_error();
				while ($business = mysql_fetch_assoc($results)) {
					extract($business); ?>
					<div class="widget">
						<div class="block-holder">
							<div class="block-frame">
								<div class="block-content">
									<h2>
										<a href="/c/<?php echo urlencode(stripslashes($safe_name)); ?>/<?php echo urlencode($zipCity); ?>/<?php echo urlencode($zipState); ?>">
											<?php echo stripslashes($name); ?>
										</a>
									</h2>
									<?php if ($large_image) {?>
										<img src="/images/businesses/<?php echo $large_image; ?>" width="605" height="222" alt="image description" class="aligncenter" />
									<?php } ?>
									<div class="block">
										<div class="info">
										    <?php if ($logo && file_exists("images/businesses/$logo")) {?>
											<div class="partners-logo">
												<img src="/images/businesses/<?php echo $logo; ?>" style="max-width:179px; height:89px; margin: auto;" alt="business logo" />
											</div>
										    <?php } ?>
										    <address>
												<div itemscope itemtype="http://schema.org/LocalBusiness">
													<span itemprop="name"><?php echo stripslashes($name); ?></span>
													<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
														<span itemprop="streetAddress"><?php echo $address; ?></span>
														<?php echo $address ? '<br/>' : ''; ?>
														<span itemprop="addressLocality"><?php echo $city; ?></span><?php echo ($city && $state) ? ", " : "" ; ?>
														<span itemprop="addressRegion"><?php echo $state; ?></span>
														<span itemprop="postalCode"><?php echo $zip; ?></span>
													</div>
													<?php echo ($phone) ? "P: " : "" ; ?><span itemprop="telephone"><?php echo $phone; ?></span>
													<br/>
													<?php echo ($alt_phone) ? "Alt P: " : "" ; ?><span itemprop="telephone"><?php echo $alt_phone; ?></span>
												</div>
										    </address>
										    <?php if ($website) {?>
										        <a target="_blank" rel="nofollow" href="<?php echo (substr($website, 0, 4) == "http") ? $website : "http://".$website ; ?>" class="link">Visit Website</a>
										    <?}elseif ($facebook) {?>
												<a target="_blank" rel="nofollow" href="<?php echo (substr($facebook, 0, 4) == "http") ? $facebook : "https://".$facebook ; ?>" class="link">Visit Facebook Page</a>
											<?php } ?>
										</div>
										<div class="area">
											<p><?php echo $description; ?></p>
											
										</div>
										
										<?php if ($facebook || $twitter || $instagram) { ?>
											<div class="social-links">
												<?php if ($facebook) {?>
                                                    <a target="_blank" rel="nofollow" href="<?php echo (substr($facebook, 0, 4) == 'http') ? $facebook : 'https://'.$facebook ; ?>" class=" facebook link">Visit Facebook Page</a>
                                                <?}?>
                                                <?php if ($twitter) {?>
                                                    <a target="_blank" rel="nofollow" href="<?php echo (substr($twitter, 0, 4) == 'http') ? $twitter : 'http://'.$twitter ; ?>" class=" twitter link">Visit Twitter Page</a>
                                                <?}?>
                                                <?php if ($instagram) {?>
                                                    <a target="_blank" rel="nofollow" href="<?php echo (substr($instagram, 0, 4) == 'http') ? $instagram : 'http://'.$instagram ; ?>" class="instagram link">Visit Instagram Page</a>
                                                <?}?>
                                            </div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php }?>
				
                
                
                <div>
				    <a href="#main">Back To Top</a>
				</div>
			</div>
			
			<?php include_once('includes/category-listing.php'); ?>
		</div>
	</div>
<?php include_once("includes/footer.php"); ?>