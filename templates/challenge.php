<?php
script('secsignid','login_polling');
style('secsignid','style');
?>

<form method="POST" >
	<h1>Please authenticate yourself using the SecSign ID app on your phone</h1>
	<input type="hidden" value="testtest" name="challenge">
	<p><?php
		print_unescaped('<img src="data:image/png;base64,'.$_SESSION['session']->getIconData().'">');
	?></p>
	<h1>Access Pass for:</h1><h2><?php p($_SESSION['session']->getSecSignID())?></h2>
	<p><button type="submit">
		<span>Okay</span>
	</button></p>
</form>
<input type="hidden" id="hidden_session" value="<?php
		$_SESSION['session']->getAuthSessionAsArray();
	?>">