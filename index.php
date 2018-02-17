<?php
ini_set("log_errors", 1);
ini_set("error_log", "brs-error.log");

error_reporting(0);

include_once("includes/header.php");
include_once("includes/weather.php");
include_once("includes/google_weather.php");
if (isset($_GET['geo'])) {
	$_SESSION['geo'] = 1;
}
?>
	<div id="main">
		<div class="slideshow">
			<div class="mask">
				<div class="slide set">
					<?php if (!$_SESSION['location']) {
						$time_start = microtime(true);
						//$_SESSION['location'] = get_user_location();
						$_SESSION['location'] = get_random_location();
						$time_end = microtime(true);
						$time = $time_end - $time_start;
						
						//echo "Found IP location in $time seconds\n";
					}
					
					if (!$_SESSION['browser_location']) {?>
						<script type="text/javascript">
							// Use jQuery to display useful information about our position.
							function showPosition(position) {
								var sendData = "action=set_browser_location";
								sendData += "&latitude=" + position.coords.latitude;
								sendData += "&longitude=" + position.coords.longitude;
								//alert(sendData);
								
								$.ajax({
									url: "post.php",
									global: false,
									type: "POST",
									data: sendData,
									dataType: "html",
									async:false,
									success: function(data){										
										<?php if (!$_SESSION['geo']) { ?>
											window.location = "index.php?geo=1";
										<?php } ?>
									}
								});
								/*
								$('#georesults').html(
									'<div id="map_canvas" style="float: right; width: 200px; height: 200px"></div>' +
									'<p>' 
											+ 'Latitude: ' + position.coords.latitude + '<br />'
											+ 'Longitude: ' + position.coords.longitude + '<br />'
											+ 'Accuracy: ' + position.coords.accuracy + '<br />'
											+ 'Altitude: ' + position.coords.altitude + '<br />'
											+ 'Altitude accuracy: ' + position.coords.altitudeAccuracy + '<br />'
											+ 'Heading: ' + position.coords.heading + '<br />'
											+ 'Speed: ' + position.coords.speed + '<br />'
									+ '</p>'
								);
								*/
							}
							
							function showError(error) {
								//alert('Error occurred. Error code: ' + error.code);  
							}
							
							/*
							 *	The following logic runs when the page loads:
							 */
							
							// We need to check if the browser has the correct capabilities.
							if (navigator.geolocation) {
								// If so, get the current position and feed it to exportPosition
								// (or errorPosition if there was a problem)
								navigator.geolocation.getCurrentPosition(showPosition, showError);
							} else {
								var sendData = "action=set_ip_location";
								
								$.ajax({
									url: "post.php",
									global: false,
									type: "POST",
									data: sendData,
									dataType: "html",
									async:false,
									success: function(data){
										<?php if (!$_SESSION['geo']) {?>
											window.location = "index.php?geo=1";
										<?php }?>
									}
								});
							}
						</script>
					<?php }
					
					if ($_SESSION['location']) {
						$latitude = $_SESSION['location']['latitude'];
						$longitude = $_SESSION['location']['longitude'];
						$radius = 400;
						
						$lon1 = $longitude - $radius / abs(cos(deg2rad($latitude))*69);
						$lon2 = $longitude + $radius / abs(cos(deg2rad($latitude))*69);
						$lat1 = $latitude - ($radius/69);
						$lat2 = $latitude + ($radius/69);
						
						$query = "SELECT city_scroller.*, zipCodes.City, zipCodes.State ,
							  (3958*3.1415926*sqrt((zipCodes.latitude-$latitude)*(zipCodes.latitude-$latitude) + cos(zipCodes.latitude/57.29578)*cos($latitude/57.29578)*(zipCodes.longitude-$longitude)*(zipCodes.longitude-$longitude))/180)
							  as distance
							  FROM city_scroller
							  INNER JOIN zipCodes ON (city_scroller.zip = zipCodes.ZipCode)
							  WHERE (3958*3.1415926*sqrt((zipCodes.latitude-$latitude)*(zipCodes.latitude-$latitude) + cos(zipCodes.latitude/57.29578)*cos($latitude/57.29578)*(zipCodes.longitude-$longitude)*(zipCodes.longitude-$longitude))/180) <= '$radius'
							  AND zipCodes.longitude between $lon1 and $lon2 and zipCodes.latitude between $lat1 and $lat2
							  GROUP BY city_scroller.id ORDER BY distance LIMIT 6";
					}else{
						$query = "Select city_scroller.*, zipCodes.City, zipCodes.State From city_scroller Left Join zipCodes On (city_scroller.zip = zipCodes.ZipCode) Group By city_scroller.zip Order By rand() Limit 6;";
					}
					
					$time_start = microtime(true);
					$slides = mysql_query($query);
					$time_end = microtime(true);
					$time = $time_end - $time_start;
					
					//echo "Completed first query in $time seconds\n"; ?>
                    <style>
					.geo-change-wrapper {
						position: absolute;
						top: 10px;
						z-index: 999;
						right: 10px;
						border: 8px solid #000000;
					}
					.geo-change-wrapper input {
						border: 3px solid #1f6cb4;
						margin: 0;
						padding: 4px 8px;
						font-weight: bold;
						text-transform: uppercase;
					}
                    </style>
                    <div class="geo-change-wrapper">
                    	<?php /*?><select name="changegeo" onchange="window.location='?geo='+this.value;">
                        <?php 
							$res = mysql_query("Select zip FROM `city_scroller`"); ?>
                        	<option>Change my ZIP</option>
                            <?php while($dat = mysql_fetch_assoc($res)) { ?>
                            <option value="<?php echo $dat['zip']; ?>"><?php echo $dat['zip']; ?></option>
                            <?php } ?>
                        </select><?php */?>
                        <input name="changegeo" onchange="window.location='?geo='+this.value;" placeholder="Change my ZIP" />
                    </div>
					<ul class="bxslider">					
					<?php if (mysql_num_rows($slides) < 1) {
						$query = "Select city_scroller.*, zipCodes.City, zipCodes.State From city_scroller Left Join zipCodes On (city_scroller.zip = zipCodes.ZipCode) Group By city_scroller.zip Order By rand() Limit 6;";
						
						$slides = mysql_query($query);
					} 
                    while ($slide = mysql_fetch_assoc($slides)) { ?>
						<li style="background:url('images/city_slides/<?php echo $slide['image']; ?>');">
							<strong class="title">Supporting <?php echo $slide['City']; ?> Businesses... Strengthening Our Community.</strong>
							<? if ($slide['credits']) {?>
								<strong class="sub-title"><?php echo $slide['credits']; ?></strong>
							<?}?>
						</li>
					<?}?>
                    </ul>   
                    <script type="text/javascript">
                        //var j = jQuery.noConflict();
                        $('.bxslider').bxSlider({
                            auto: true,
                            mode: 'horizontal',
                            infiniteLoop: true,
                            pause: 8000,
                            pager: false,
                            responsive: true,
                            adaptiveHeight: true,
                            hideControlOnEnd: true
                        });
                    </script>
					
					<?php if (false) {//$_SESSION['location']['zip_code']) {
						$weather = new WeatherData($_SESSION['location']['zip_code'], $units); ?>
						<?php $google_weather = new GoogleWeatherData($_SESSION['location']['zip_code']); ?>
						<div class="weather">
							<div class="weather-holder">
								<div class="weather-frame">
									<div class="weather-content">
										<img src="http://www.google.com<?php echo  $google_weather->icon; ?>" width="112" height="117" alt="weather" />
										<p><?php echo  $google_weather->city; ?><div style="padding-top: 2px;"><span class="temprature"><?php echo  $google_weather->temperature; ?>&ordm;<span class="symbol">f</span></span></div><div style="padding-top: 2px;"><?php echo  $google_weather->condition; ?><span style="float: left;">(<?php echo  $_SESSION['location']['zip_code']; ?>)</span></div></p>
									</div>
								</div>
							</div>
						</div>
					<?}?>
				</div>
			</div>
		</div>
		<div class="intro">
			<div class="intro-holder">
				
				<div class="partners">
					<div class="head">
						<div class="switcher">
							<a href="#" class="btn-prev">previous</a>
							<a href="#" class="btn-next">next</a>
							<strong class="slider-counter">
								<span class="cur-slide">1</span> of <span class="all-slide">30</span>
							</strong>
						</div>
						<h2><strong class="txt-featured">featured!</strong> businesses</h2>
					</div>
					
					<div class="mask-holder">
						<div class="mask">
							<div class="slideset">
								<?php if ($_SESSION['location']) {
									$latitude = $_SESSION['location']['latitude'];
									$longitude = $_SESSION['location']['longitude'];
									$radius = 75;
									
									$lon1 = $longitude - $radius / abs(cos(deg2rad($latitude))*69);
									$lon2 = $longitude + $radius / abs(cos(deg2rad($latitude))*69);
									$lat1 = $latitude - ($radius/69);
									$lat2 = $latitude + ($radius/69);
									
									$query = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState,
									((3958*3.1415926*sqrt((zipCodes.latitude-$latitude)*(zipCodes.latitude-$latitude) + cos(zipCodes.latitude/57.29578)*cos($latitude/57.29578)*(zipCodes.longitude-$longitude)*(zipCodes.longitude-$longitude))/180)) as distance
									FROM business
									INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode)
									WHERE (3958*3.1415926*sqrt((zipCodes.latitude-$latitude)*(zipCodes.latitude-$latitude) + cos(zipCodes.latitude/57.29578)*cos($latitude/57.29578)*(zipCodes.longitude-$longitude)*(zipCodes.longitude-$longitude))/180) <= '$radius'
									AND zipCodes.longitude between $lon1 and $lon2 and zipCodes.latitude between $lat1 and $lat2
									AND topFeatured = 1									
									AND business.active = 1
									GROUP BY id, category_id
									ORDER BY Case When topFeatured > 0 Then 1 Else 2 End, distance LIMIT 0, 30";
									
									
								}else{
									$query = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState
									FROM business
									INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode)
									WHERE topFeatured = 1 AND business.active = 1 GROUP BY id, category_id ORDER BY rand() LIMIT 0, 30";
									
									
								}
								
								$time_start = microtime(true);
								$results = mysql_query($query);
								
								if (mysql_num_rows($results) < 1) {
									$query = "SELECT business.*, zipCodes.City as zipCity, zipCodes.State as zipState
									FROM business
									INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode)
									WHERE topFeatured = 1 AND business.active = 1 GROUP BY id, category_id ORDER BY rand() LIMIT 0, 30";
									
									$results = mysql_query($query);
								}
								
								$time_end = microtime(true);
								$time = $time_end - $time_start;
								
								//echo "Completed second query in $time seconds\n";
								
								while ($slide = mysql_fetch_assoc($results)) {
									$slide = stripSlashesFromArray($slide);
									extract($slide); ?>
									<div class="slide">
										<div class="info">
											<?php if (($index_logo && file_exists('images/businesses/'.$index_logo)) || ($logo && file_exists('images/businesses/'.$logo))) {?>
												<img src="images/businesses/<?php echo  ($index_logo) ? $index_logo : $logo ; ?>" style="max-width:195px; height:166px;" alt="<?php echo  $name; ?>" class="image" />
											<?php }?>
										</div>
										<div class="textbox">
											<strong class="title"><a href="/c/<?php echo urlencode(stripslashes($safe_name)); ?>/<?php echo urlencode($zipCity); ?>/<?php echo urlencode($zipState); ?>"><?php echo $name; ?></a></strong>
											<p><?php echo substr(strip_tags($description), 0, 520); ?>...</p>
											<a href="/c/<?php echo urlencode(stripslashes($safe_name)); ?>/<?php echo urlencode($zipCity); ?>/<?php echo urlencode($zipState); ?>" class="btn-more">READ MORE</a>
										</div>
									</div>
								<?}?>
							</div>
						</div>
					</div>
				</div>
                
                <div class="services">
					<div class="head">
						<strong class="logo"><a href="#">BRS</a></strong>
						<h2><strong class="txt-grow">Grow!</strong> your business &amp; services with us</h2>
					</div>
					<div class="services-content">
						<p>
							Whether you are a small business or a large corporation, all businesses need to maintain a loyal consumer base as well as keep public
							awareness. Advertising plays a significant role in stimulating company growth, building consumer confidence and maintaining customer
							loyalty.
						</p>
						<p>
							It would be easy to get new customers if we all had an unlimited marketing budget. But for the majority of us, this is just not the case.
						</p>
						<p>
							Let's face it in these economic times, most business owners are short on two things: Time and Money. <!--Let us take the hassles and
							large expense out of advertising while you're still focusing on what makes you money ... running your business!-->
						</p>
						<a href="our-services.php" class="btn-more">SEE WHAT BRS CAN DO FOR YOU</a>
					</div>
				</div>
			</div>
		</div>
		<div class="panel">
			<div class="panel-holder">
				<div class="box">
					<img src="images/icon2.png" width="80" height="107" alt="image description" class="icon" />
					<a href="contactus.php" class="link">TIME IS OF THE ESSENCE<span>Sign up to Become an EXCLUSIVE<br/>Business in your city TODAY!</span></a>
				</div>
				<div class="box">
					<img src="images/icon.png" width="80" height="107" alt="image description" class="icon" />
					<a href="/services/" class="link">FEATURED BUSINESSES<span>Check out our current<br/>featured businesses today!</span></a>
				</div>
			</div>
		</div>
		<div class="three-columns">
			<div class="column">
				<div class="column-holder">
					<div class="column-frame">
						<ul class="services">
						
							<li>
								<strong class="title">OUR SERVICES</strong>
								<p><strong>Business Review's</strong> annual publication <strong>"Supporting Local Businesses ... Strengthening Our Community"</strong>,
								is a free standing, self-mailing publication.</p>
								<a href="our-services.php" class="btn-more">READ MORE</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="column">
				<div class="column-holder">
					
					<div class="column-frame">
						
						<div class="reviews">
							<h3><a href="contactus.php">CONTACT US TODAY!</a></h3>
							<a href="publication.php"><img src="images/img4.png" width="232" height="256" alt="Business Reviews" class="aligncenter" /></a>
						</div>
						
					</div>
				</div>
			</div>
			<div class="column">
				<div class="column-holder">
					<div class="column-frame">
						<div class="box">
							<img style="margin: -10px 0 -15px 0;" src="images/shop-local.png" width="110" height="135" alt="image description" class="alignleft" />
							<div class="textbox">
								<p><strong>It's important to SUPPORT YOUR LOCAL BUSINESSES!</strong></p>
								<a class="slogan" style="font-weight: bold;" href="support.php">Read More</a>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
<?php include_once("includes/footer.php"); ?>