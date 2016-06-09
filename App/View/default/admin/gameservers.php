<div class="content_pad">
	<h1>Administration Section</h1>
	<ul id="admin-nav">
		<li><a href="/admin" class="selected">Manage Game Servers</a> </li>
		<li><a href="/admin/template/">Manage Game Server Templates</a> </li>
		<li><a href="/admin/dedicated/">Manage Dedicated Servers</a> </li>
		<li><a href="/admin/user/">Manage Users</a> </li>
	</ul>
	<h2>Game Servers</h2>
	<table class="admin-table" style="width: 100%;">
		<thead>
			<tr>
				<td width="30px" class="center">ID</td>
				<td class="phone-hidden">Dedicated Server</td>
				<td class="phone-hidden">Template</td>
				<td class="phone-hidden">Owner</td>
				<td class="phone-hidden">Nickname</td>
				<td>IP/Port</td>
				<td class="phone-hidden center">Status</td>
				<td width="220px" class="right">Actions</td>
			</tr>
		</thead>
		<tbody>
			<?php foreach($servers as $server): ?>
			<tr>
				<td class="center"><?=$server['gs_id']?></td>
				<td class="phone-hidden">
					<?php if(!empty($server['ds_id'])): ?>
						<a href="/admin/dedicated-edit/<?=$server['ds_id']?>"><?=$server['ds_nickname']?></a>
					<?php else: ?>
						(no dedicated server)
					<?php endif; ?>
				</td>
				<td class="phone-hidden">
					<?php if(!empty($server['gst_id'])): ?>
						<a href="/admin/template-edit/<?=$server['gst_id']?>"><?=$server['gst_type']?></a>
					<?php else: ?>
						(no template)
					<?php endif; ?>
				</td>
				<td class="phone-hidden">
					<?php if(!empty($server['user_id'])): ?>
						<a href="/admin/user-edit/<?=$server['user_id']?>"><?=$server['user_username']?> (<?=$server['user_name']?>)</a>
					<?php else: ?>
						(no user)
					<?php endif; ?>
				</td>
				<td class="phone-hidden"><?=$server['nickname']?></td>
				<td><?=$server['ip']?>:<?=$server['port']?></td>
				<td class="phone-hidden center"><div class="gs-status" data-status="<?=GameServerStatus::$STYLE_TEXT[$server['online_status']]?>">&nbsp;</div></td>
				<td><a href="/dashboard/manage/<?=$server['gs_id']?>" class="manage-button">&nbsp;</a> <a href="/admin/gameserver-edit/<?=$server['gs_id']?>" class="edit-button">&nbsp;</a> <a href="/admin/gameserver-delete/<?=$server['gs_id']?>" class="delete-button">&nbsp;</a></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<a href="/admin/gameserver-edit/new" class="create_new btn-sub-table">Create New</a>
	<a href="/admin" class="refresh btn-sub-table">Refresh</a>
</div>