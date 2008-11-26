<?php
$color = config::get("profiler.colors.$key");
$buildstring = '<a name="'.strtolower($key).'"></a><fieldset id="bm_'.strtolower($key).'" style="border: 1px solid '.$color.'; background: #EEE; margin-bottom: 2em; padding: .5em 1em 1em;">
	<legend style="color: '.$color.'; padding: 0 .5em; border: 1px solid '.$color.'; background: #EEE; margin-top: 0; line-height: 200%;">'.ucwords($key).'</legend>
	<table cellspacing="1" style="background: #FFF; color: '.$color.'; width: 100%;">';
$buildstring .= '<thead><tr style="text-align: left;"><th style="background: #CCC;">File</th><th style="background: #CCC;">Line</th><th style="background: #CCC;">Value</th></tr></thead>';	
foreach ($infoArray as $key => $value) {
	$val = print_r($value['value'], true);
	$buildstring .= '<tr><td style="background: #DDD;">'.$value['file'].'</td><td style="background: #DDD;">'.$value['line'].'</td><td style="background: #DDD;"><pre>'.$val.'</pre></td></tr>';
}
$buildstring .= '<tbody>';
$buildstring .= '</tbody></table>
</fieldset>';

echo $buildstring;
?>