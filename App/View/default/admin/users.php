<div class="content_pad">
	<h1>Administration Section</h1>
	<ul id="admin-nav">
		<li><a href="/admin">Manage Game Servers</a> </li>
		<li><a href="/admin/template/">Manage Game Server Templates</a> </li>
		<li><a href="/admin/dedicated/">Manage Dedicated Servers</a> </li>
		<li><a href="/admin/user/" class="selected">Manage Users</a> </li>
	</ul>
	<h2>Users</h2>
	<table class="admin-table" style="width: 100%;">
		<thead>
		<tr>
			<td width="30px" class="center">ID</td>
			<td >Username</td>
			<td>Name</td>
			<td width="130px" class="right">Actions</td>
		</tr>
		</thead>
		<tbody>
		<?php foreach($users as $user): ?>
			<tr>
				<td class="center"><?=$user['user_id']?></td>
				<td><?=$user['username']?></td>
				<td><?=$user['first_name']?> <?=$user['last_name']?></td>
				<td> <a href="/admin/user-edit/<?=$user['user_id']?>" class="edit-button">&nbsp;</a> <a href="/admin/user-delete/<?=$user['user_id']?>" class="delete-button">&nbsp;</a></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<a href="/admin/user-edit/new" class="create_new btn-sub-table">Create New</a>
</div>