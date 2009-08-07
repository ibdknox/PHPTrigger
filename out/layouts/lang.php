<html>
	<head>
		<title>Slider - <?php echo isset($title) ? $title : ''; ?></title>
		<?php echo tag::style('slider'); ?>
	</head>
	<body>
		<div>
			<p>Try me.. I dare you</p>
			<?php echo $yield; ?>
		</div>
	</body>
</html>