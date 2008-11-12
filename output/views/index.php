<?php $this->useTemplate('test'); ?>
<form action="/cool/test" method="post">
	<input type="hidden" name="formName" value="login" />
	<dl>
		<dt>username</dt>
			<dd><input name="username" type="text" /></dd>
	</dl>
	<input type="submit" />
</form>