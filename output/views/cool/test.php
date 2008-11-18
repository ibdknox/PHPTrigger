<?php $title = 'login'; $this->useTemplate('test'); ?>
<p>this is a test <?php echo $this->get('test::info'); ?></p>
<ul>
	<?php foreach($this->get('test::woot') as $li) { ?>
	<li><?php echo $li; ?></li>
	<?php } ?>
</ul>