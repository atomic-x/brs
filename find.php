<form method="get">
	<input type="hidden" name="doFind" value="true" />
	<p>String to Find: <input type="text" size="100" name="strToFind" value="<?php echo isset($_GET['strToFind']) ? $_GET['strToFind'] : ''; ?>" /></p>
    <p>Find in Folder: <input type="text" size="100" name="folderToFind" value="<?php echo isset($_GET['folderToFind']) ? $_GET['folderToFind'] : 'onlinefilipinos.com'; ?>" /></p>
    <p><input type="submit" value="Find" /></p>
</form>

<?php
if(isset($_GET['doFind']) && $_GET['doFind'] == 'true')
{
	$str = isset($_GET['strToFind']) ? $_GET['strToFind'] : '';
	$folder = isset($_GET['folderToFind']) ? $_GET['folderToFind'] : '';
	$definition = isset($_GET['defined']) ? $_GET['defined'] : false;
	$path = realpath(__DIR__ . '/'.$folder.'/'); // Path to your textfiles 
	$fileList = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
	
	foreach ($fileList as $item) 
	{
		if ($item->isFile() && (stripos($item->getPathName(), 'php') !== false || stripos($item->getPathName(), 'js') !== false)) 
		{
			$file_contents = file_get_contents($item->getPathName());
			//$file_contents = str_replace("localhost","localhost/loginux",$file_contents);
			//file_put_contents($item->getPathName(),$file_contents);
			if (strpos($file_contents,$str) !== false) {
				echo $item->getPathName();
				echo '<br>';
			}
		}
	}
}
?>
