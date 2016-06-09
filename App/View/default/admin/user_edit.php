<div class="content_pad">
	<h1>Administration Section</h1>
	<ul id="admin-nav">
		<li><a href="/admin">Manage Game Servers</a> </li>
		<li><a href="/admin/template/">Manage Game Server Templates</a> </li>
		<li><a href="/admin/dedicated/">Manage Dedicated Servers</a> </li>
		<li><a href="/admin/user/"  class="selected">Manage Users</a> </li>
	</ul>
	<?php if($submit && $error): ?>
		<div class="admin-error"><strong>Opps there was a problem processing your request!</strong><br/><?=$errormsg?></div>
	<?php elseif($submit): ?>
		<div class="admin-success">You have sucessfully updated the user!</div>
	<?php endif; ?>
	<form class="admin_form" method="POST" action="/admin/user-edit/<?=!isset($user) ? 'new' : $user->GetId();?>">
		<div><strong>ID</strong> <?=!isset($user) ? 'new' : $user->GetId();?></div>
		<label>
			<p>Username</p>
			<input name="username" type="text" value="<?=!isset($user) ?  $post['username'] : $user->GetUsername()?>" />
		</label>
		<label>
			<p>E-Mail</p>
			<input name="email" type="text" value="<?=!isset($user) ?  $post['email'] : $user->GetEmail()?>" />
		</label>
		<label>
			<p>First Name</p>
			<input name="firstname" type="text" value="<?=!isset($user) ?  $post['firstname'] : $user->GetFirstname()?>" />
		</label>
		<label>
			<p>Last Name</p>
			<input name="lastname" type="text" value="<?=!isset($user) ?  $post['lastname'] : $user->GetLastname()?>" />
		</label>
		<label>
			<p>Password</p>
			<input name="password" type="password" value="<?=$post['password']?>" />
		</label>

		<input class="submit" type="submit" name="submit" value="Save">
	</form>
</div>