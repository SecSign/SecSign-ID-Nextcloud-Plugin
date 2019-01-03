<div class="section" id='sec'>
	<h2> SecSign 2FA </h2>
	<h1>The two-factor-authentication by SecSign allows you to set a SecSign ID for each of your users to ensure a safe login.</h1>
</div>
<script id="content-tpl" type="text/x-handlebars-template">
	<h2> User Management </h2>
	<table border='1|1'>
		<tr>
			<td><?php p($l->t('User ID')); ?></td>
			<td><?php p($l->t('SecSign ID')); ?></td>
			<td><?php p($l->t('New ID')); ?></td>
		</tr>
		{{#each users}}
			<tr>
				<td>id</td>
				<td>secsignid</td>
				<td><?php p($l->t('Enter ID')); ?></td>
			</tr>
		{{/each}}
	</table>
</script>
<div id="page" class="section"></div>
<!--{{#each users}}
			<tr>
				<td>id</td>
				<td>secsignid</td>
				<td><?php p($l->t('Enter ID')); ?></td>
			</tr>
		{{/each}} -->