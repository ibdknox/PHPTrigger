<html>
	<head>
        <script type="text/javascript"
        src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.js"> </script>
		<?php echo tag::style('default'); ?>
        <?php echo tag::script('blog'); ?>
	</head>
	<body class="empty">
        <div class="navi">
            <a href="">Filter</a>
        </div>
        <div class="outerwrapper">
            <?php echo $yield; ?>
            <div class="nav">
                <ul>
                    <li><a href="/lang">blog</a></li>
                    <li><a href="/links">links</a></li>
                    <li><a href="/creative">creative</a></li>
                    <li><a href="/projects">projects</a></li>
                </ul>
                <div class="navi">
                    <a href="">Prev</a>
                    <a href="">Next</a>
                </div>
            </div>
        </div>
	</body>
</html>
