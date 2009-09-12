<html>
	<head>
		<title>Pics - <?php echo $this->get('gallery::group'); ?></title>
		<?php echo tag::style('default'); ?>
	</head>
	<body>	
			<?php foreach($this->get('gallery::pics') as $pic) { ?>
				<img src='<?php echo $pic; ?>' />
			<?php } ?>
	</body>
</html>