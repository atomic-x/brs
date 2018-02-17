<?php ob_start();
include_once("includes/db_connect.php");
include_once("includes/functions.php");
$results = mysql_query("SELECT DISTINCT business.*, zipCodes.City as zipCity, zipCodes.State as zipState FROM business INNER JOIN zipCodes ON (business.listZip = zipCodes.zipCode) Order By business.name;");

header("content-type: text/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?><?xml-stylesheet type="text/xsl" href="http://businessreviewservices.com/css/sitemap.xsl"?>'; ?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
		<url>
			<loc>http://<?= $_SERVER['SERVER_NAME']; ?>/</loc>
			<lastmod><?= date("c"); ?></lastmod>
			<changefreq>daily</changefreq>
			<priority>1</priority>
		</url>
		<url>
			<loc>http://<?= $_SERVER['SERVER_NAME']; ?>/publication.php</loc>
			<lastmod><?= date("c"); ?></lastmod>
			<changefreq>monthly</changefreq>
			<priority>1</priority>
		</url>
		<url>
			<loc>http://<?= $_SERVER['SERVER_NAME']; ?>/our-services.php</loc>
			<lastmod><?= date("c"); ?></lastmod>
			<changefreq>monthly</changefreq>
			<priority>1</priority>
		</url>
		<url>
			<loc>http://<?= $_SERVER['SERVER_NAME']; ?>/listings.php</loc>
			<lastmod><?= date("c"); ?></lastmod>
			<changefreq>daily</changefreq>
			<priority>1</priority>
		</url>
		<url>
			<loc>http://<?= $_SERVER['SERVER_NAME']; ?>/webdesign.php</loc>
			<lastmod><?= date("c"); ?></lastmod>
			<changefreq>monthly</changefreq>
			<priority>0.6</priority>
		</url>
		<url>
			<loc>http://<?= $_SERVER['SERVER_NAME']; ?>/qrcodes.php</loc>
			<lastmod><?= date("c"); ?></lastmod>
			<changefreq>monthly</changefreq>
			<priority>0.6</priority>
		</url>
		<url>
			<loc>http://<?= $_SERVER['SERVER_NAME']; ?>/contactus.php</loc>
			<lastmod><?= date("c"); ?></lastmod>
			<changefreq>monthly</changefreq>
			<priority>0.6</priority>
		</url>
		<url>
			<loc>http://<?= $_SERVER['SERVER_NAME']; ?>/testimonials.php</loc>
			<lastmod><?= date("c"); ?></lastmod>
			<changefreq>monthly</changefreq>
			<priority>0.6</priority>
		</url>
		<url>
			<loc>http://<?= $_SERVER['SERVER_NAME']; ?>/support.php</loc>
			<lastmod><?= date("c"); ?></lastmod>
			<changefreq>monthly</changefreq>
			<priority>0.6</priority>
		</url>
	<? ob_flush();
	while ($result = mysql_fetch_assoc($results)) {
		extract(stripSlashesFromArray($result)); ?>
		<url>
			<loc>http://<?= $_SERVER['SERVER_NAME']; ?>/c/<?= urlencode(stripslashes($safe_name)); ?>/<?= urlencode($zipCity); ?>/<?= urlencode($zipState); ?></loc>
			<lastmod><?= date("c"); ?></lastmod>
			<changefreq>monthly</changefreq>
			<priority>0.6</priority>
		</url>
		<? ob_flush();
	}?>
</urlset>
<? ob_end_flush(); ?>