<div class="content_pad">
    <h1>Administration Section</h1>
    <ul id="admin-nav">
        <li><a href="/admin">Manage Game Servers</a></li>
        <li><a href="/admin/template/" class="selected">Manage Game Server Templates</a></li>
        <li><a href="/admin/dedicated/">Manage Dedicated Servers</a></li>
        <li><a href="/admin/user/">Manage Users</a></li>
    </ul>
    <h2>Game Server Templates</h2>
    <table class="admin-table" style="width: 100%;">
        <thead>
        <tr>
            <td width="30px" class="center">ID</td>
            <td>Name</td>
            <td width="130px" class="right">Actions</td>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($templates as $template): ?>
            <tr>
                <td class="center"><?= $template['template_id'] ?></td>
                <td><?= $template['long_name'] ?></td>
                <td><a href="/admin/template-edit/<?= $template['template_id'] ?>" class="edit-button">&nbsp;</a> <a
                        href="/admin/template-delete/<?= $template['template_id'] ?>" class="delete-button">&nbsp;</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="/admin/template-edit/new" class="create_new btn-sub-table">Create New</a>
</div>