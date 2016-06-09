<div class="content_pad">
    <h1>Administration Section</h1>
    <ul id="admin-nav">
        <li><a href="/admin">Manage Game Servers</a></li>
        <li><a href="/admin/template/">Manage Game Server Templates</a></li>
        <li><a href="/admin/dedicated/" class="selected">Manage Dedicated Servers</a></li>
        <li><a href="/admin/user/">Manage Users</a></li>
    </ul>
    <?php if ($submit && $error): ?>
        <div class="admin-error"><strong>Opps there was a problem processing your request!</strong><br/><?= $errormsg ?>
        </div>
    <?php elseif ($submit): ?>
        <div class="admin-success">You have sucessfully updated the Dedicated Server!</div>
    <?php endif; ?>

    <form class="admin_form" method="POST"
          action="/admin/dedicated-edit/<?= !isset($server) ? 'new' : $server->GetId(); ?>">
        <div><strong>ID</strong> <?= !isset($server) ? 'new' : $server->GetId(); ?></div>
        <label>
            <p>Main Ip</p>
            <input name="main_ip" type="text" value="<?= !isset($server) ? $post['main_ip'] : $server->GetMainIp() ?>"/>
        </label>

        <label>
            <p>Nickname</p>
            <input name="nickname" type="text"
                   value="<?= !isset($server) ? $post['nickname'] : $server->GetNickname() ?>"/>
        </label>

        <label>
            <p>SSH Ip</p>
            <input name="ssh_ip" type="text" value="<?= !isset($server) ? $post['ssh_ip'] : $server->GetSSHIp() ?>"/>
        </label>

        <label>
            <p>SSH Port</p>
            <input name="ssh_port" type="text"
                   value="<?= !isset($server) ? $post['ssh_port'] : $server->GetSSHPort() ?>"/>
        </label>

        <label>
            <p>SSH Username</p>
            <input name="ssh_username" type="text"
                   value="<?= !isset($server) ? $post['ssh_username'] : $server->GetSSHUsername() ?>"/>
        </label>
        <label>
            <p>SSH Password</p>
            <input name="ssh_password" type="password"
                   value="<?= !isset($server) ? $post['ssh_password'] : $server->GetSSHPassword() ?>"/>
        </label>

        <label>
            <p>Game Files Directory</p>
            <input name="installs_dir" type="text"
                   value="<?= !isset($server) ? $post['installs_dir'] : $server->GetInstallDir() ?>"/>
        </label>
        <input class="submit" type="submit" name="submit" value="Save">
        <br clear="all"/>
    </form>
    <br clear="all"/>
</div>