<div class="content_pad">
	<h1>Manage Account (<?=$user->GetUsername()?>)</h1>
	<form action="" id="account-form" class="account-form">
		<label style="float:left;">
			<p>First Name</p>
			<input type="text" id="account-firstname" value="<?=$user->GetFirstName()?>" />
		</label>
		<label style="float:right;">
			<p>Last Name</p>
			<input type="text" id="account-lastname" value="<?=$user->GetLastName()?>"/>
		</label>
		<label style="float:left;">
			<p>E-Mail</p>
			<input type="text" id="account-email" value="<?=$user->GetEmail()?>" />
		</label>
		<br clear="all" />

		<label style="float:left;">
			<p>Password</p>
			<input type="password"  id="account-pw" />
		</label>
		<label style="float:right;">
			<p>Confirm</p>
			<input type="password" id="account-pw-confirm"/>
		</label>
		<button id="account-submit" class="submit">Save Changes</button>
		<div id="ma-loading"><img src="/assets/img/loading-small.gif" /></div>
		<div id="ma-done"><img src="/assets/img/icons/tick.png" /> Done</div>
	</form>
	<br clear="all"/>
</div>