<?php $title = 'login'; $this->useTemplate('test'); ?>
<ul>
	<?php foreach($this->get('test::woot') as $li) { ?>
	<li><?php echo $li; ?></li>
	<?php } ?>
</ul>
<p>this is a test <?php echo $this->get('test::info'); ?></p>
