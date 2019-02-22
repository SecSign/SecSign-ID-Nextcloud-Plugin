<!--
- @author SecSign Technologies Inc.
- @copyright 2019 SecSign Technologies Inc.
-->
<?php
script('secsignid','secsign_user_management');
style('secsignid','lds_roller');
style('secsignid','tablestyle');
?>

<div class="section" id='sec'>
	<h2>SecSign 2FA User Management </h2>
	<p>View all users and manage their SecSign ID two-factor authentication. Changes can be saved using the "Save" button
		below.
	</p>
	<div id="changes" >
		<h2 id="total_changes">Total changes: 0</h2><button id="save_changes">Save changes</button> 
	</div>
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
	<div>
		<table id="table" hidden>
			<thead>
				<tr>
					<th>Username</th>
					<th>Display name</th>
					<th>SecSign ID</th>
					<th>2FA Enabled</th>
					<th id='edited' hidden>Edited</th>
				</tr>
			</thead>
			<tbody id="tbody">
		</table>

	</div>
</div>