<?php
script('secsignid', 'login_polling');
style('secsignid','style');
?>

<form method="POST" >
	<h1 style="margin-bottom: 15px">Please authenticate yourself using the SecSign ID app on your phone</h1>
	<input type="hidden" value="testtest" name="challenge">
	<div id="secsignid-accesspass-container"><?php
		print_unescaped('<img id="secsignid-accesspass-img" src="data:image/png;base64,'.$_SESSION['session']->getIconData().'">');
	?></div>
	<h1>Access Pass for:</h1><h2 id="secsignid"><?php p($_SESSION['session']->getSecSignID())?></h2>
	<p><button id="button" type="submit">
		<span>Okay</span>
	</button></p>
</form>