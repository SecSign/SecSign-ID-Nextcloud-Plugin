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
	<h2 style="text-decoration: underline">SecSign 2FA Settings</h2>
	<div>
		<h2>User Permissions</h2>
		<div style="margin: 16px">
			<input type='checkbox' class='checkbox' disabled id='allow_user_enable'>
			<label for='allow_user_enable'>Allow users to edit their SecSign 2FA settings (enable or disable 2FA, edit SecSign ID, etc.)</label>
		</div>
	</div>
	<div>
		<h2>User Management</h2>
		<p>View all users and manage their SecSign ID two-factor authentication. Changes can be saved using the "Save" button
		below.</p>
		<p id="enforced_warning" hidden style="color: var(--color-warning);">Some users with enforced two-factor authentication do not have a SecSign ID assigned. This will prevent them from logging in.</p>
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
</div>