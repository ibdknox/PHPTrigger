<html>
	<head>
		<title>this is a test layout - <?php echo isset($title) ? $title : ''; ?></title>
		<?php echo tag::style('default'); ?>
	</head>
	<body>
		<div>
			<p>welcome to the layout</p>
			<?php echo $yield; ?>
		</div>
	</body>
</html>