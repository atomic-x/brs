<?php include_once("includes/functions.php");



if (isset($_GET['id'])){

    $id = intval($_GET['id']);

	

	$result = mysql_fetch_assoc(mysql_query("SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState, zipCodes.Latitude, zipCodes.Longitude, categories.name as category_name

											FROM business

											Left Join zipCodes On business.listZip = zipCodes.ZipCode

											Left Join categories On business.category_id = categories.id

											Where business.id = $id"));

}else{

	//echo $_SERVER['REQUEST_URI'];

	$path = explode('/', $_SERVER['REQUEST_URI']);

	//print_r($path);

	$company = str_replace('[and]', '/', urldecode($path[2]));

	$city = str_replace('[and]', '/', urldecode($path[3]));

	$state = str_replace('[and]', '/', urldecode($path[4]));

	

	$query = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState, zipCodes.Latitude, zipCodes.Longitude, categories.name as category_name

				FROM business

				Left Join zipCodes On business.listZip = zipCodes.ZipCode

				Left Join categories On business.category_id = categories.id

				Where business.safe_name = '$company'

					And zipCodes.City = '$city'

					And zipCodes.State = '$state';";

	

	$result = mysql_fetch_assoc(mysql_query($query));

	

	//echo mysql_error().'<br/>'.$query;

}



extract(stripSlashesFromArray($result));



mysql_query("Update business Set views = (views + 1) Where id = '$id'");



$header_array = array('title' => $zipCity.', '.$zipState.' Business Directory | '.$category_name.' - '.$name,

					  'description' => '',

					  'keywords' => '');



include_once("includes/header.php");

?>

	<div id="main">

		<div class="twocolumns">

			<div id="content">

				<div class="head">

					<div class="form-search">

						<div class="form-holder">

							<strong>SEARCH BY ZIP CODE:</strong>

						</div>

						<div class="box">

							<div class="box-holder">

								<form action="/services/">

									<fieldset>

										<label for="zip">ENTER YOUR ZIP CODE:</label>

										<div class="text">

											<input type="text" id="zip" name="zip" />

											<input type="hidden" name="mcat" value="<?php echo  $mcat_id; ?>" />

											<input type="hidden" name="cat" value="<?php echo  $cat_id; ?>" />

										</div>

										<input type="submit" value="SEARCH" />

									</fieldset>

								</form>

							</div>

						</div>

					</div>

				</div>

				

				<h1>

					<?php echo  stripslashes($name); ?>

					<?php if ($website) { ?>

						&nbsp;

						(

						<a style="font-size: 14px;" target="_blank" rel="nofollow" href="<?php echo  (substr($website, 0, 4) == "http") ? $website : "http://".$website ; ?>" class="link">

							Visit Website

						</a>

						)

					<?}?>

				</h1>

				<br style="clear: both;" />

				<h2>Servicing <?php echo  $listZip; ?> and nearby areas</h2>

				

				<?php if ($large_image) { ?>

					<img style="margin: 20px auto;" src="/images/businesses/<?php echo  $large_image; ?>" width="605" height="222" alt="<?php echo  $name; ?> banner" class="aligncenter" />

				<?}?>

				

				<div class="block">

					<div class="info">

						<address style="float: right; text-align: right;">

							<div itemscope itemtype="http://schema.org/LocalBusiness">

								<span itemprop="name" style="font-size: 16px; font-weight: bold;"><?php echo  stripslashes($name); ?></span>

								<?php if ($website) { ?>

									<br/>

									<a target="_blank" rel="nofollow" href="<?php echo  (substr($website, 0, 4) == "http") ? $website : "http://".$website ; ?>" class="link">

										<?php echo  stripslashes($website); ?>

									</a>

								<?}?>

								<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">

									<span itemprop="streetAddress"><?php echo  $address; ?></span>

									<br/>

									<span itemprop="addressLocality"><?php echo  $city; ?></span><?php echo  ($city && $state) ? ", " : "" ; ?>

									<span itemprop="addressRegion"><?php echo  $state; ?></span>

									<span itemprop="postalCode"><?php echo  $zip; ?></span>

								</div>

								<?php echo  ($phone) ? "P: " : "" ; ?><span itemprop="telephone"><?php echo  $phone; ?></span>

								<br/>

								<?php echo  ($alt_phone) ? "Alt P: " : "" ; ?><span itemprop="telephone"><?php echo  $alt_phone; ?></span>

							</div>

						</address>

						

						<?php if ($website) { ?><a target="_blank" rel="nofollow" href="<?php echo  (substr($website, 0, 4) == "http") ? $website : "http://".$website ; ?>" class="link"><?}?>

						

						<?php if (!empty($logo) && file_exists("images/businesses/$logo")) { ?>

							<div class="partners-logo" style="display: inline-block;">

								<img src="/images/businesses/<?php echo  $logo; ?>" alt="<?php echo  $name; ?> logo" />

							</div>

						<?}?>

						

						<?php if ($website) { ?></a><?}?>

					</div>

					

					<img class="map-image" alt="<?php echo  $zipCity; ?>, <?php echo  $zipState; ?> <?php echo  $category_name; ?> <?php echo  $name; ?>" src="https://maps.googleapis.com/maps/api/staticmap?center=<?php echo  $Latitude; ?>,<?php echo  $Longitude; ?>&zoom=12&size=633x300&maptype=roadmap&markers=color:red|<?php echo  $Latitude; ?>,<?php echo  $Longitude; ?>&key=AIzaSyBjSQApCmSfQr1uNhkWKJSl4Jwpeg7gfZ0" />

					<!--

					<div class="map">

						<div id="map-canvas"></div>

						

						<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBjSQApCmSfQr1uNhkWKJSl4Jwpeg7gfZ0"></script>

						<script type="text/javascript">

							function initialize() {

								var myLatlng = new google.maps.LatLng(<?php echo  $Latitude; ?>, <?php echo  $Longitude; ?>);

								

								var mapOptions = {

									center: myLatlng,

									zoom: 12

								};

								var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

								

								var marker = new google.maps.Marker({

									position: myLatlng,

									map: map,

									title:"<?php echo  stripslashes($name); ?>"

								});

								

								var contentString = '<div id="content">'+

													'<h1 id="firstHeading" class="firstHeading"><?php echo  stripslashes($name); ?></h1>'+

													'<div id="bodyContent">'+

													'<p>' +

													'Servicing <?php echo  $listZip; ?> and near-by areas' +

													'</p>' +

													'</div>' +

													'</div>';

								

								var infowindow = new google.maps.InfoWindow({

									content: contentString

								});

								

								google.maps.event.addListener(marker, 'click', function() {

									infowindow.open(map,marker);

								});

								

								infowindow.open(map,marker);

							}

							google.maps.event.addDomListener(window, 'load', initialize);

						</script>

					</div>

					-->

					<div class="area">

						<p><?php echo  $description; ?></p>

					</div>

					

					<?php echo  $accreditations ? "<p><b>Accreditations:</b> $accreditations</p>" : "" ; ?>

					

					<?php echo  $hours ? "<p><b>Hours</b>: $hours</p>" : "" ; ?>

					

					<?php echo  $owner ? "<p><b>Owner</b>: $owner</p>" : "" ; ?>

					

					<?php echo  $insured ? "<p>Company Is Insured</p>" : "" ; ?>

					

					<?php echo  $free_estimate ? "<p>Company Provides Free Estimates</p>" : "" ; ?>

					

					<?php echo  $emergency_service ? "<p>Has 24 Hour Emergency Service</p>" : "" ; ?>

					

					<?php if ($facebook || $twitter || $instagram) { ?>

						<div class="social-links">

							<?php if ($facebook) { ?>

								<a target="_blank" rel="nofollow" class="facebook link" href="<?php echo  (substr($facebook, 0, 4) == "http") ? $facebook : "https://".$facebook ; ?>">Visit Facebook Page</a>

							<?}?>

							<?php if ($twitter) { ?>

								<a target="_blank" rel="nofollow" class="twitter link" href="<?php echo  (substr($twitter, 0, 4) == "http") ? $twitter : "http://".$twitter ; ?>">Visit Twitter Page</a>

							<?}?>

							<?php if ($instagram) { ?>

								<a target="_blank" rel="nofollow" class="instagram link" href="<?php echo  (substr($instagram, 0, 4) == "http") ? $instagram : "http://".$instagram ; ?>">Visit Instagram Page</a>

							<?}?>

						</div>

					<?}?>

				</div>

			</div>

			

			<?php include_once('includes/category-listing.php'); ?>

		</div>

	</div>

<?php include_once("includes/footer.php"); ?>