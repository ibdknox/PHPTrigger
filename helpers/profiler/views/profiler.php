<div style="clear:both;">
<?php 
$path = HELPERSDIR.'/profiler/views/';
foreach(profiler::$info as $key => $infoArray) { 
	
	if(!empty($infoArray)) {
		if(file_exists($path.$key.'.php')) {
			include($path.$key.'.php');
		} else {
			include($path.'default.php');
		}
	}
	
} 
?>
</div>