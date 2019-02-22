<!--
- @author SecSign Technologies Inc.
- @copyright 2019 SecSign Technologies Inc.
-->
<?php
script('secsignid','secsignid_settings');
style('secsignid','lds_roller');
style('secsignid','settings');
?>

<div class="section" id='sec'>
	<h2> SecSign 2FA </h2>
	<div class="lds-roller">
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
		<div></div>
	</div>
	<div id="enabled" hidden>
		<p id="description"> You have already added a SecSign ID protecting your account.</p>
		SecSign ID: <input id="secsignid_input_en" type="text" name="secsignid">
		<button id="change_id" type="button">Update</button>
		<button id="disable" type="button">Disable</button>
		<div></div>
	</div>
	<div id="disabled" hidden>
		<h1> Enable two factor authentication with your SecSign ID </h1>
		SecSign ID: <input id="secsignid_input_dis" type="text" name="secsignid">
		<button id="enable_id" type="button">Submit</button>
	</div>
</div>