<?php $this->useTemplate('test'); ?>
<p>this is a test <?php echo $this->_('test::info'); ?></p>
<ul>
	<?php foreach($this->_('test::woot') as $li) { ?>
	<li><?php echo $li; ?></li>
	<?php } ?>
</ul>