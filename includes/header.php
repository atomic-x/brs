<?php
include_once("includes/functions.php");

if (!count($header_array)) {
	$header_array = array('title' => 'Business Review Services',
						  'description' => '',
						  'keywords' => '',
						  'content' => '');
}
$path = explode('/', $_SERVER['REQUEST_URI']);
//print_r($path);
$category = str_replace('[and]', '/', urldecode($path[2]));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="title" content="<?= $header_array['title']; ?>" />
	<meta name="description" content="<?= $header_array['description']; ?>" />
	<meta name="keywords" content="<?= $header_array['keywords']; ?>" />
	<script>  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');  ga('create', 'UA-59398937-1', 'auto');  ga('send', 'pageview');</script>
	<title><?= $header_array['title']; ?></title>
	
	
    <!-- bxSlider CSS file -->
    <link href="/css/jquery.bxslider.css" type="text/css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" media="all" href="/css/all.css" />
	<link rel="stylesheet" type="text/css" media="all" href="/css/forms.css" />
    <link rel="stylesheet" type="text/css" media="all" href="/css/media.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    
	<script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="/js/jquery.bxslider.min.js"></script>
    <script type="text/javascript" src="/js/ui/jquery-ui.js"></script>
	<script type="text/javascript" src="/js/jquery.main.js"></script>
	<!--[if IE 8]><link rel="stylesheet" type="text/css" media="all" href="/css/ie.css" /><![endif]-->
	<script type="text/javascript">
		$(function() {
		var availableTags = [
			<?php 
			$sql = "SELECT * FROM mcat ORDER BY mcat_name ASC;";
			$mcname = 'mcat_name';
			/*if($mcat_id) {
				$sql = "SELECT categories.*, mcat.mcat_name, mcat.safe_name as mcat_safe_name FROM categories Inner Join mcat On (categories.mcat_id = mcat.mcat_id) WHERE categories.mcat_id='$mcat_id' ORDER BY categories.name ASC;";
				$name = 'name';
			}*/
			$categories = mysql_query($sql);
			
			while($row = mysql_fetch_assoc($categories)) { 
				echo '"'.$row['safe_name'].'",';
				$ssql = "SELECT categories.*, mcat.mcat_name, mcat.safe_name as mcat_safe_name FROM categories Inner Join mcat On (categories.mcat_id = mcat.mcat_id) WHERE categories.mcat_id='".$row['mcat_id']."' ORDER BY categories.name ASC;";
				$scategories = mysql_query($ssql);
				if($scategories) {
					while($srow = mysql_fetch_assoc($scategories)) { 
						echo '"'.$row['safe_name'].' / '.$srow['safe_name'].'",';
					}
				}
			} ?>
		  	""
		];
		$("#categorysearch").autocomplete({
			source: availableTags,
			autoFocus: true,
			change: function( event, ui ) {
				var catval = $("#categorysearch").val();
				var catcln = catval.replace(/ /g,'');
				$("#categoryform").attr('action','http://businessreviewservices.com/services/' + catcln + '/<?php echo $_SESSION['zip']; ?>');
			}
		});
  });
    </script>
	<?php echo $header_array['content']; ?>
</head>
<body class="<?php echo $mcat_id; ?>">
    <div id="mySidenav" class="sidenav">
      <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <h2>Menu</h2>
        <ul>
            <li><a href="http://businessreviewservices.com/">Home</a></li>
            <li><a href="http://businessreviewservices.com/aboutus.php">About Us</a></li>
            <li class="<?= ($page == "featured") ? "active" : "" ; ?>">
                <a href="/services/"><span>Featured</span>Businesses</a>
            </li>
            <li class="<?= ($page == "services") ? "active" : "" ; ?>">
                <a href="/our-services.php"><span>Our</span>Services</a>
            </li>
            <li><a href="http://businessreviewservices.com/webdesign.php">Website Design</a></li>
            <li class="<?= ($page == "publications") ? "active" : "" ; ?>">
              <a href="/publication.php"><span>Our</span>Publications</a>
            </li>
            <li><a href="http://businessreviewservices.com/contactus.php">QR Codes</a></li>
            <li><a href="http://businessreviewservices.com/testimonials.php">Testimonials</a></li>
            <li><a href="http://businessreviewservices.com/contactus.php">Contact Us</a></li>
            <li><a href="http://businessreviewservices.com/careers.php">Careers</a></li>
            <li><a href="http://businessreviewservices.com/testimonials.php">Private Policy</a></li>
        </ul>
    </div>
	<div id="header">
		<div class="header-holder">
			<div class="header-frame">
				<div class="logoarea">
					<strong class="logo"><a href="/index.php">Business Review Services, INC</a></strong>
					<address>632 Nilles Rd. Fairfield, OH 45014 &bull; (513) 887-5344</address>
				</div>
				<strong class="phone">CALL TOLL FREE: 1-800-669-3736</strong>
				<div class="info">
					<a href="/index.php" class="link">Home</a><em class="date"><?= date("m/d/y"); ?></em><em class="date"><a style="color: #CC0000;" href="/careers.php" class="link">careers</a></em>
				</div>
			</div>
		</div>
		<div id="nav">
            <span class="mobmenu" onclick="openNav()">&#9776; Menu</span>
			<ul class="mobihid">
				<li class="<?= ($page == "publications") ? "active" : "" ; ?>"><a href="/publication.php"><em><strong><span>Our</span>Publications</strong></em></a></li>
				<li class="<?= ($page == "services") ? "active" : "" ; ?>"><a href="/our-services.php"><em><strong><span>Our</span>Services</strong></em></a></li>
				<li class="<?= ($page == "featured") ? "active" : "" ; ?><?//= " hasdrop"; ?>"><a href="/services/"><em><strong><span>Featured</span>Businesses</strong></em></a>
				</li>
			</ul>
		</div>
	</div>