<!--
- @author SecSign Technologies Inc.
- @copyright 2019 SecSign Technologies Inc.
-->
<?php
script('secsignid','secsign_user_management');
style('secsignid','lds_roller');
style('secsignid','tablestyle');
?>

<div class="section" id="sec">
	<h2 style="padding-left: 12px">SecSign ID</h2>
	<div class="secsign-content">
		<nav id="nav-heading">
			<div class="navgroup">
				<ul class="nav-ul">
					<li>
						<a class="selected" id="btn_management">
							<span>User Management</span>
						</a>
					</li>
					<li>
						<a id="btn_permissions">
							<span>User permissions</span>
						</a>
					</li>
					<li>
						<a id="btn_settings">
							<span>Configuration</span>
						</a>
					</li>
					<li>
						<a id="btn_onboarding">
							<span>Onboarding</span>
						</a>
					</li>
				</ul>
			</div>
		</nav>
		<div id="user_permissions" class="tabcontent" style="display: none">
			<div>
				<p style="margin-bottom: 8px">Choose whether non-admin users are allowed edit their SecSign ID settings.
					This includes enabling and disabling 2FA as well as editing their assigned SecSign ID. Note that
					enforced 2FA will still be active, even if a user has disabled 2FA in their settings.</p>
				<input type='checkbox' class='checkbox' disabled id='allow_user_enable'>
				<label for='allow_user_enable'>Allow users to edit SecSign ID settings</label>
				<button id="save_allow_enable" hidden>Save</button>
			</div>
		</div>
		<div id="user_management" class="tabcontent" style="display: block">
			<p>View all users and manage their SecSign ID two-factor authentication. Changes can be saved using the
				"Save" button
				below.</p>
			<p id="enforced_warning" hidden style="color: var(--color-warning);">Some users with enforced two-factor
				authentication do not have a SecSign ID assigned. This will prevent them from logging in.</p>
			<div id="changes">
				<h1 id="total_changes">Total changes: 0</h2><button id="save_changes">Save changes</button>
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
		<div id="secsign_settings" class="tabcontent" style="display: none">
			<p style="margin-top: 8px; margin-bottom: 8px; font-size: 110%;text-decoration: underline">SecSign ID
				server: </p>
			<p>Edit this setting to change which ID server will be used. This is necessary to connect to an in-house
				server for example. This URL must point to the HTTP API endpoint.</p>
			<p>Adding a fallback server is also useful in case something goes wrong.</p>
			<p>Notice that changing this setting may cause users to be unable to log in if they already have a SecSign
				ID assigned which does not exist on the new server.</p>
			<div style="margin-top: 8px">
				<label style="margin-right: 9px" for="ssid_server">Server:</label>
				<input class="server_input server" type='text' placeholder="SecSign ID server" id='ssid_server'>
				<label for="ssid_server_port">Port:</label>
				<input class="server_input port" type='text' placeholder="Server port" id='ssid_server_port'>
			</div>
			<div>
				<label for="ssid_fallback">Fallback:</label>
				<input class="server_input server " type='text' placeholder="SecSign ID server" id='ssid_fallback'>
				<label for="ssid_fallback_port">Port:</label>
				<input class="server_input port" type='text' placeholder="Fallback port" id='ssid_fallback_port'>
				<button id="save_server" hidden>Save</button>
			</div>
			<p>To implement User Onboarding on an in-house server, please enter the URL to the mobile API. This is not
				the same as the URL for the HTTP API. If you are having issues with this, please contact
				support@secsign.com for help.</p>
			<div style="margin-top: 8px">
				<label style="margin-right: 9px" for="ssid_server_mobile">Server:</label>
				<input class="server" type='text' placeholder="SecSign ID server" id='ssid_server_mobile'>
				<button id="save_server_mobile" hidden>Save</button>
			</div>
		</div>
		<div id="user_onboarding" class="tabcontent" style="display: none">
			<div>
				<p style="margin-bottom: 8px">This option allows you to activate SecSign 2FA for all users without
				having to create and enter a SecSign ID for each user. Simply choose a suffix and all users will be
				prompted to create a SecSign ID consisting of their username + '@suffix‘ when they try to log in the next time. If the automatically
				assigned SecSign ID exists already, the username will be incremented.</p>
				<div>
					<input type='checkbox' class='checkbox' id='enable_onboarding'>
					<label for='enable_onboarding'>Enable onboarding for all users.</label>					
				</div>
				<div>
					<label class="onboarding_input" style="margin-right: 9px" for="onboarding_suffix">Suffix:</label>
					<input class="server onboarding_input" type='text' placeholder="for example: 'accounting'" id='onboarding_suffix'>
					<p class="onboarding_input" id="onboarding_example">Schema example: john.doe@accounting</p>
					<button id="save_onboarding" hidden>Save</button>
				</div>
			</div>
		</div>
	</div>
</div>