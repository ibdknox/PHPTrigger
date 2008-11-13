<html>
	<head>
		<title>this is a test layout</title>
		<?php echo tag::style('default'); ?>
	</head>
	<body>
		<div>
			<p>welcome to the layout</p>
			<?php echo $yield; ?>
		</div>
	</body>
</html>