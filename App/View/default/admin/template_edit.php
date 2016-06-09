<div class="content_pad">
    <h1>Administration Section</h1>
    <ul id="admin-nav">
        <li><a href="/admin">Manage Game Servers</a></li>
        <li><a href="/admin/template/" class="selected">Manage Game Server Templates</a></li>
        <li><a href="/admin/dedicated/">Manage Dedicated Servers</a></li>
        <li><a href="/admin/user/">Manage Users</a></li>
    </ul>
    <?php if ($submit && $error): ?>
        <div class="admin-error"><strong>Opps there was a problem processing your request!</strong><br/><?= $errormsg ?>
        </div>
    <?php elseif ($submit): ?>
        <div class="admin-success">You have sucessfully updated the Game Temlate!</div>
    <?php endif; ?>
    <form class="admin_form" method="POST"
          action="/admin/template-edit/<?= !isset($template) ? 'new' : $template->GetId(); ?>">
        <div><strong>ID</strong> <?= !isset($template) ? 'new' : $template->GetId(); ?></div>
        <label>
            <p>Name</p>
            <input name="long_name" type="text"
                   value="<?= !isset($template) ? $post['long_name'] : $template->GetName() ?>"/>
        </label>

        <label>
            <p>Minimum Slots</p>
            <input name="min_slots" type="text"
                   value="<?= !isset($template) ? $post['min_slots'] : $template->GetMinSlots() ?>"/>
        </label>

        <label>
            <p>Maximum Slots</p>
            <input name="max_slots" type="text"
                   value="<?= !isset($template) ? $post['max_slots'] : $template->GetMaxSlots() ?>"/>
        </label>

        <label>
            <p>Default Slots</p>
            <input name="default_slots" type="text"
                   value="<?= !isset($template) ? $post['default_slots'] : $template->GetDefaultSlots() ?>"/>
        </label>

        <label>
            <p>Default Port</p>
            <input name="default_port" type="text"
                   value="<?= !isset($template) ? $post['default_port'] : $template->GetDefaultPort() ?>"/>
        </label>


        <label>
            <p>Default Startup Command</p>
            <input name="default_cmd" type="text"
                   value="<?= !isset($template) ? $post['default_cmd'] : $template->GetDefaultCmd() ?>"/>
        </label>


        <label>
            <p>Game Zip File</p>
            <input name="game_files_zip" type="text"
                   value="<?= !isset($template) ? $post['game_files_zip'] : $template->GetZipFilename() ?>"/>
        </label>

        <label>
            <p>Connection Url</p>
            <input name="connection_url" type="text"
                   value="<?= !isset($template) ? $post['connection_url'] : $template->GetConnectionUrl() ?>"/>
        </label>

        <input class="submit" type="submit" name="submit" value="Save">
    </form>
</div>