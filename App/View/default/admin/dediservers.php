<div class="content_pad">
	<h1>Administration Section</h1>
	<ul id="admin-nav">
		<li><a href="/admin">Manage Game Servers</a> </li>
		<li><a href="/admin/template/">Manage Game Server Templates</a> </li>
		<li><a href="/admin/dedicated/" class="selected">Manage Dedicated Servers</a> </li>
		<li><a href="/admin/user/">Manage Users</a> </li>
	</ul>
	<h2>Dedicated Servers</h2>
	<table class="admin-table" style="width: 100%;">
		<thead>
		<tr>
			<td width="30px" class="center">ID</td>
			<td>Nickname</td>
			<td class="phone-hidden">Main IP</td>
			<td width="130px" class="right">Actions</td>
		</tr>
		</thead>
		<tbody>
		<?php foreach($servers as $server): ?>
			<tr>
				<td class="center"><?=$server['dserver_id']?></td>
				<td><?=$server['nickname']?></td>
				<td class="phone-hidden"><?=$server['main_ip']?></td>
				<td> <a href="/admin/dedicated-edit/<?=$server['dserver_id']?>" class="edit-button">&nbsp;</a> <a href="/admin/dedicated-delete/<?=$server['dserver_id']?>" class="delete-button">&nbsp;</a></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<a href="/admin/dedicated-edit/new" class="create_new btn-sub-table">Create New</a>
</div>