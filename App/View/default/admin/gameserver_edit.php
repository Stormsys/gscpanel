<div class="content_pad">
    <h1>Administration Section</h1>
    <ul id="admin-nav">
        <li><a href="/admin" class="selected">Manage Game Servers</a></li>
        <li><a href="/admin/template/">Manage Game Server Templates</a></li>
        <li><a href="/admin/dedicated/">Manage Dedicated Servers</a></li>
        <li><a href="/admin/user/">Manage Users</a></li>
    </ul>
    <?php if ($submit && $error): ?>
        <div class="admin-error"><strong>Opps there was a problem processing your request!</strong><br/><?= $errormsg ?>
        </div>
    <?php elseif ($submit): ?>
        <div class="admin-success">You have sucessfully updated the Game Server!</div>
    <?php endif; ?>
    <form class="admin_form" method="POST" action="/admin/gameserver-edit/<?= !isset($gs) ? 'new' : $gs->GetId(); ?>">
        <div><strong>ID</strong> <?= !isset($gs) ? 'new' : $gs->GetId(); ?> <?php if (isset($gs) && !empty($gs)): ?>(<a
                href="/dashboard/manage/<?= $gs->GetId(); ?>">click
                                                              here</a>  to stop/start/restart or reinstall the server.)<?php endif; ?>
        </div>
        <label>
            <p>Nickname</p>
            <input name="nickname" type="text" value="<?= !isset($gs) ? $post['nickname'] : $gs->GetNickname() ?>"/>
        </label>

        <label>
            <p>Dedicated Server</p>
            <select name="ds_id">
                <option></option>
                <?php foreach ($dservers as $dserver): ?>
                    <option
                        value="<?= $dserver['dserver_id'] ?>" <?= (isset($gs) && $gs->GetDedicatedServerId() == $dserver['dserver_id']) || (!isset($gs) && isset($post['ds_id']) && $dserver['dserver_id'] == $post['ds_id']) ? 'selected' : '' ?>><?= $dserver['nickname'] ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            <p>Template(Game)</p>
            <select name="template_id">
                <option></option>
                <?php foreach ($templates as $template): ?>
                    <option
                        value="<?= $template['template_id'] ?>" <?= (isset($gs) && $gs->GetTemplateId() == $template['template_id']) || (!isset($gs) && isset($post['template_id']) && $template['template_id'] == $post['template_id']) ? 'selected' : '' ?>><?= $template['long_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            <p>Owner</p>
            <select name="owner-id">
                <option></option>
                <?php foreach ($users as $user): ?>
                    <option
                        value="<?= $user['user_id'] ?>" <?= (isset($gs) && $gs->GetOwnerId() == $user['user_id']) || (!isset($gs) && isset($post['owner-id']) && $user['user_id'] == $post['owner-id']) ? 'selected' : '' ?>><?= $user['username'] ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            <p>IP</p>
            <input name="ip" type="text" value="<?= !isset($gs) ? $post['ip'] : $gs->GetIp() ?>"/>
        </label>

        <label>
            <p>Port</p>
            <input name="port" type="text" value="<?= !isset($gs) ? $post['port'] : $gs->GetPort() ?>"/>
        </label>

        <label>
            <p>Player Slots</p>
            <input name="slot_count" type="text" value="<?= !isset($gs) ? $post['slot_count'] : $gs->GetSlots() ?>"/>
        </label>

        <br clear="all"/>

        <label>
            <p>Startup CMD</p>
            <input name="paramater_overide" type="text"
                   value="<?= !isset($gs) ? $post['paramater_overide'] : $gs->GetCommandLine() ?>"/>
        </label>

        <?php if (isset($gs) && !empty($gs)): ?>
            <label>
                <p>Server Dir</p>
                <?= !isset($gs) ? '' : $gs->GetServerDir() ?>
            </label>


            <label>
                <p>Ftp User</p>
                <?= !isset($gs) ? '' : $gs->GetFtpUser() ?>
            </label>

            <label>
                <p>Ftp Password</p>
                <input name="ftp_password" type="password"
                       value="<?= !isset($gs) ? $post['ftp_password'] : $gs->GetFtpPass() ?>"/>
            </label>
        <?php endif; ?>
        <input class="submit" type="submit" name="submit" value="Save">
        <br clear="all"/>
    </form>
</div>