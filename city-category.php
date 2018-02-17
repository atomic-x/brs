<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

if (stristr($_SERVER['REQUEST_URI'], '/companies/') && !stristr($_SERVER['REQUEST_URI'], '/companies/?')) {
	//echo $_SERVER['REQUEST_URI'];
	$path = explode('/', $_SERVER['REQUEST_URI']);
	//print_r($path);
	$city = urldecode($path[2]);
	$state = getStateAbbreviatedName(urldecode($path[3]));
	$category = urldecode($path[4]);
	
	$query = "Select zip_code_category_data.*
				From zip_code_category_data
				Inner Join mcat On (zip_code_category_data.category_id = mcat.mcat_id)
				Where zip_code_category_data.city = '".mysql_real_escape_string($city)."'
					And zip_code_category_data.state = '".mysql_real_escape_string($state)."'
					And mcat.safe_name = '".mysql_real_escape_string($category)."'
				Limit 1;";
	$zip_category_data = mysql_fetch_assoc(mysql_query($query));
	
	$query = "SELECT business.*
				FROM business
				INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode)
				INNER JOIN categories ON (business.category_id = categories.id)
				INNER JOIN mcat ON (categories.mcat_id = mcat.mcat_id)
				Where mcat.safe_name = '$category'
					And zipCodes.City = '$city'
					And zipCodes.State = '$state'
				Group By business.id
				Order By rand()
				Limit 12";
	
	//echo $query;
	$results = mysql_query($query);
	//echo mysql_error();
	if (mysql_num_rows($results) > 4) {
		$query = "SELECT zipCode, latitude, longitude
					FROM zipCodes
					Where zipCodes.City = '$city'
						And zipCodes.State = '$state'
					Group By zipCode
					Order By rand()
					Limit 1";
		
		list($zip, $latitude, $longitude) = mysql_fetch_array(mysql_query($query));
		
		if ($zip) {
			$radius = 50;
			
			$lon1 = $longitude - $radius / abs(cos(deg2rad($latitude))*69);
			$lon2 = $longitude + $radius / abs(cos(deg2rad($latitude))*69);
			$lat1 = $latitude - ($radius/69);
			$lat2 = $latitude + ($radius/69);
			
			$query = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState, zipCodes.longitude,zipCodes.latitude, mcat.mcat_id, mcat.mcat_name, 
						(3956 * 2 * ASIN ( SQRT (POWER(SIN((zipCodes.latitude - $latitude)*pi()/180 / 2),2) + COS(zipCodes.latitude* pi()/180) * COS($latitude *pi()/180) * POWER(SIN((zipCodes.longitude - $longitude) *pi()/180 / 2), 2) ) )) as distance
						FROM business
						INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode)
						INNER JOIN categories ON (business.category_id = categories.id)
						INNER JOIN mcat ON (categories.mcat_id = mcat.mcat_id)
						WHERE business.active = 1
						And (3958*3.1415926*sqrt((zipCodes.latitude-$latitude)*(zipCodes.latitude-$latitude) + cos(zipCodes.latitude/57.29578)*cos($latitude/57.29578)*(zipCodes.longitude-$longitude)*(zipCodes.longitude-$longitude))/180) <= '$radius'
						And zipCodes.longitude between $lon1 and $lon2 and zipCodes.latitude between $lat1 and $lat2 
						And mcat.safe_name = '$category'
						GROUP BY business.id
						ORDER BY distance
						LIMIT 12";
		}
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

$header_array = array('title' => $zip_category_data['page_header_title'],
						  'description' => $zip_category_data['page_header_description'],
						  'keywords' => $zip_category_data['page_header_keywords'],
						  'content' => $zip_category_data['page_header_content']);

include_once("includes/header.php"); ?>
	<div id="main">
		<div class="m20p">
			<h2 class="title">
				<?php echo $zip_category_data['page_title']; ?>
			</h2>
			
			<br style="clear: both;" />
			
			<?php echo $zip_category_data['page_content']; ?>
			
			<br style="clear: both;" />
			
			<? while ($business = mysql_fetch_assoc($results)) {
				extract($business); ?>
				<div class="cat-list-item">
					<div class="cat-list-item-border">
						<div class="image-holder">
							<? if ($logo && file_exists("images/businesses/$logo")) {?>
								<a href="/c/<?= urlencode(stripslashes($safe_name)); ?>/<?= urlencode($zipCity); ?>/<?= urlencode($zipState); ?>">
									<img src="/images/businesses/<?= $logo; ?>" style="max-width:179px; height:89px; margin: auto;" alt="<?= stripslashes($name); ?> logo" />
								</a>
							<?}?>
						</div>
						<div class="info-holder">
							<div class="block">
								<div class="info">
									<address>
										<div itemscope itemtype="http://schema.org/LocalBusiness">
											<span itemprop="name">
												<a href="/c/<?= urlencode(stripslashes($safe_name)); ?>/<?= urlencode($zipCity); ?>/<?= urlencode($zipState); ?>">
													<?= stripslashes($name); ?>
												</a>
											</span>
											<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
												<span itemprop="streetAddress"><?= $address; ?></span>
												<?= $address ? '<br/>' : ''; ?>
												<span itemprop="addressLocality"><?= $city; ?></span><?= ($city && $state) ? ", " : "" ; ?>
												<span itemprop="addressRegion"><?= $state; ?></span>
												<span itemprop="postalCode"><?= $zip; ?></span>
											</div>
											<?= ($phone) ? "P: " : "" ; ?><span itemprop="telephone"><?= $phone; ?></span>
											<br/>
											<?= ($alt_phone) ? "Alt P: " : "" ; ?><span itemprop="telephone"><?= $alt_phone; ?></span>
										</div>
									</address>
									<? if ($website) {?>
										<a target="_blank" rel="nofollow" href="<?= (substr($website, 0, 4) == "http") ? $website : "http://".$website ; ?>" class="link">Visit Website</a>
									<?}elseif ($facebook) {?>
										<a target="_blank" rel="nofollow" href="<?= (substr($facebook, 0, 4) == "http") ? $facebook : "https://".$facebook ; ?>" class="link">Visit Facebook Page</a>
									<?}?>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?}?>
			
			<br style="clear: both;" />
		</div>
	</div>
<? include_once("includes/footer.php"); ?>