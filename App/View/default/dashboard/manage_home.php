<div id="sub-header">
    <ul id='breadcrumb'>
        <li><a href="/dashboard">Dashboard</a></li>
        <li><a href="#">GameServer #<?= $server->GetId() ?></a></li>
    </ul>
</div>
<div id="content">
    <div id="server-info" data-gs-id="<?= $server->GetId() ?>"
         data-status="<?= GameServerStatus::$STYLE_TEXT[$server->GetStatus()] ?>">
        <input value="<?= $server->GetNickname() ?>" size="<?= strlen($server->GetNickname()) + 1 ?>"
               class="mo-input h1"/>
        <span id="server-status-manage">&nbsp;</span>
        <div></div>
        <br clear="all"/>
        <div class="actions-container main-action">
            <a href="#/server/<?= $server->GetId() ?>/start" id="btn-start" class="server-action-main">Start</a>

            <a href="#/server/<?= $server->GetId() ?>/restart" id="btn-restart" class="server-action-main">Restart</a>
            <a href="#/server/<?= $server->GetId() ?>/stop" id="btn-stop" class="server-action-main">Stop</a>

        </div>
        <div class="actions-container">
            <a href="ftp://<?= $server->GetFtpUser() ?>:<?= $server->GetFtpPass() ?>@<?= $server->GetIp() ?>:21"
               target="_blank" class="server-action-main">Connect FTP</a>
        </div>

        <div class="actions-container-r">
            <a href="#/server/<?= $server->GetId() ?>/reinstall" id="btn-reinstall"
               class="server-action-main">Reinstall</a>
            <a href="/admin/gameserver-edit/<?= $server->GetId() ?>" class="server-action-main">Admin Manage</a>
        </div>


        <br clear="all"/>
        <ul class="manage-server-details">
            <li><strong>Type</strong> Counter-Strike: Source</li>
            <li><strong>IP/Port</strong> <?= $server->GetIp() ?>:<?= $server->GetPort() ?></li>
            <li><strong>Player Slots</strong> <?= $server->GetSlots() ?></li>
            <li class="spacer">&nbsp;</li>

            <li><strong>FTP Host</strong> <?= $server->GetIp() ?>:21</li>
            <li><strong>FTP User</strong> <?= $server->GetFtpUser() ?></li>
            <li><strong>FTP Pass</strong> <?= $server->GetFtpPass() ?></li>
        </ul>
        <br clear="all"/>
        <div id="file-man-holder">
            <script>
                if (window.GSCP)
                    GSCP.ViewControllers.dashboard.NavigateFileBrowser(<?=$server->GetId()?>, '');
                else {
                    setTimeout(function () {
                        GSCP.ViewControllers.dashboard.NavigateFileBrowser(<?=$server->GetId()?>, '');
                    }, 1800);
                }

            </script>
        </div>
        <br clear="all"/>
        <br clear="all"/>
        <br clear="all"/>
        <br clear="all"/>
    </div>
</div>