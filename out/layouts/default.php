<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Opened. - <?php echo config::get('site.title'); ?></title>
		<?php echo tag::style('default'); ?>
	</head>
	<body>
		<div class="content">
			<div class="header">
                <h1><a href="/"><img src="/stateful/assets/images/openedtext.gif" alt="Opened." /></a></h1>
                <ul>
                    <li><?php echo tag::a('blog/', 'blog'); ?></li>
                    <li><?php echo tag::a('links/', 'links'); ?></li>
                    <li><?php echo tag::a('creative/', 'creative'); ?></li>
                    <li><?php echo tag::a('projects/', 'projects'); ?></li>
                    <li><?php echo tag::a('admin/', 'admin'); ?></li>
                </ul>
			</div>
			<div class="subhead">
				<p><img src="/stateful/assets/images/subheadertext1.gif" alt="We are what we make of ourselves..." />
				<span><img src="/stateful/assets/images/subheadertext2.gif" alt="and this is what I've made: musings, ramblings, technology, poetry, music, life." /></span></p>
			</div>
			
			<?php echo $yield; ?>
		</div>
	</body>
</html>
