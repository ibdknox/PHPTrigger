<?php
$color = config::get("profiler.colors.$key");
$buildstring = '<a name="'.strtolower($key).'"></a><fieldset id="bm_'.strtolower($key).'" style="border: 1px solid '.$color.'; background: #EEE; margin-bottom: 2em; padding: .5em 1em 1em;">
	<legend style="color: '.$color.'; padding: 0 .5em; border: 1px solid '.$color.'; background: #EEE; margin-top: 0; line-height: 200%;">'.ucwords($key).'</legend>
	<table cellspacing="1" style="background: #FFF; color: '.$color.'; width: 100%;">';
foreach ($infoArray as $key => $value) {
	if (is_array($value)) {
		$buildstring .= '<tr><td style="width: 25%; background: #DDD;">'.treat::xss($value[0]).'</td><td style="width: 75%; background: #DDD;">'.treat::xss($value[1]).'</td></tr>';
	} else {
		$buildstring .= '<tr><td style="width: 25%; background: #DDD;">'.treat::xss($key).'</td><td style="width: 75%; background: #DDD;">'.treat::xss($value).'</td></tr>';
	}
}
$buildstring .= '<tbody>';
$buildstring .= '</tbody></table>
</fieldset>';

echo $buildstring;
?>