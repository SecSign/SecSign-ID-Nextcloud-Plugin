<!--
- @author SecSign Technologies Inc.
- @copyright 2019 SecSign Technologies Inc.
-->
<?php
script('secsignid','secsign_user_management');
style('secsignid','tablestyle');
?>

<div id="app-navigation">
	<ul>
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
<div id="app-content" class="secsign-content">
	<div id="user_permissions" class="tabcontent" style="display: none">
		<h2 class="sec_content_header">User Permissions</h2>
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
		<h2 class="sec_content_header">User Management</h2>
		<p>View all users and manage their SecSign ID two-factor authentication. Changes can be saved using the
			"Save changes" button
			below.</p>
		<p id="enforced_warning" hidden style="color: var(--color-warning);">Some users with enforced two-factor
			authentication do not have a SecSign ID assigned. This will prevent them from logging in unless Onboarding
			is activated.</p>
		<div id="changes">
			<button id="save_changes" style="display: none;">Save 0 changes</button>
		</div>

		<div class="secUi-main__barload">
			<div class="secUi-custbgcolor"></div>
			<div class="secUi-custbgcolor"></div>
			<div class="secUi-custbgcolor"></div>
		</div>
		<div class="table" hidden>
			<div class="sec_filter">
				<label for="sec_select_group" id="sec_select_label">Filter by group:</label>
				<select id="sec_select_group">
					<option id="sec_select_all" value="All groups">All groups</option>
				</select>
			</div>
			<div class="sec_search">
				<label for="sec_search_input">Search for user:</label>
				<input id="sec_search_input" type="text" placeholder="Username, displayname or SecSign ID">
			</div>
			<table id="table">
				<thead>
					<tr id="sec_header_row">
						<th id="sec-th-username">
							<span>Username</span>
							<span class="sort_indicator"></span>
						</th>
						<th id="sec-th-displayname">
							<span>Display name</span>
							<span class="sort_indicator"></span>
						</th>
						<th id="sec-th-secsignid">
							<span>SecSign ID</span>
							<span class="sort_indicator"></span>
						</th>
						<th id="sec-th-2fa">
							<span>2FA Enabled</span>
							<span class="sort_indicator"></span>
						</th>
						<th id="sec-th-edited" id='edited' hidden>
							<span>Edited</span>
							<span class="sort_indicator"></span>
						</th>
					</tr>
				</thead>
				<tbody id="tbody">
			</table>
		</div>
	</div>
	<div id="secsign_settings" class="tabcontent" style="display: none">
		<h2 class="sec_content_header">Configuration</h2>
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
			<input class="server_input port" type='number' max='65535' min='0' placeholder="Server port"
				id='ssid_server_port'>
		</div>
		<div>
			<label for="ssid_fallback">Fallback:</label>
			<input class="server_input server " type='text' placeholder="SecSign ID server" id='ssid_fallback'>
			<label for="ssid_fallback_port">Port:</label>
			<input class="server_input port" type='number' max='65535' min='0' placeholder="Fallback port"
				id='ssid_fallback_port'>
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
		<h2 class="sec_content_header">Onboarding</h2>
		<div>
			<p style="margin-bottom: 8px">This option allows you to activate SecSign 2FA for all users without having to create and enter a SecSign ID for each user. Simply choose a suffix and all users will be prompted to create a SecSign ID consisting of their username + '@suffix‘ when they try to log in the next time. Or allow them to choose an ID. Make sure that <a style="text-decoration: underline" id='two_factor_auth_link'>Two-Factor authentication</a> is enforced or enabled for all users that you wish to assign a SecSign ID to.</p>
			<div>
				<h3>Enable Onboarding</h3>
				<div>
					<input type='checkbox' class='checkbox' id='enable_onboarding'>
					<label for='enable_onboarding'>Enable onboarding for all users.</label>
				</div>
				<div>
					<input type='checkbox' class='checkbox' id='enable_onboarding_choice'>
					<label for='enable_onboarding_choice'>Allow users to choose a SecSign ID during onboarding</label>
				</div>
			</div>
			<div class="onboarding_input">
				<h3>Onboarding Suffix</h3>
				<p>This schema will be applied to all users that don't choose their own SecSign ID</p>
				<div>
					<label class="onboarding_input" style="margin-right: 9px" for="onboarding_suffix">Suffix:</label>
					<span class="onboarding_input text_field_border">
						<span>Username@</span>
						<input class="server onboarding_input" type='text' placeholder="for example: 'accounting'" id='onboarding_suffix' maxlength="25">
					</span>
					<div style="margin-top: 4px">
						<p class="onboarding_input">Schema example:   <b id="onboarding_example">John.doe@accounting</b></p>
					</div>
					<button id="save_onboarding" hidden>Save</button>
				</div>
			</div>
		</div>
	</div>

</div>