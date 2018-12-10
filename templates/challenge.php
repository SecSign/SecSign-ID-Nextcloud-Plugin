<?php
style('secsignid','style');
?>

<form method="POST" >
	<h1>Please authenticate yourself using the SecSign ID app on your phone</h1>
	<input type="hidden" value="testtest" name="challenge">
	<p><?php
		print_unescaped('<img src="data:image/png;base64,'.$_SESSION['session']->getIconData().'">');
	?></p>
	<p><button type="submit">
		<span>Okay</span>
	</button></p>
</form>