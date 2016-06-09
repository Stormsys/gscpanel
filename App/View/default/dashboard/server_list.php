<div id="sidebar-tools">
    Add Category
</div>
<div id="sidebar">
    <ul>
        <li class="droppable">Counter-Strike <span class="blue-bubble">2</span></li>
        <li class="selected droppable">Source Games <span class="blue-bubble">4</span></li>
        <li class="droppable">All Servers</li>
    </ul>
</div>
<div id="content">
    <ul id="server-list">

        <!-- Start Server Box Template -->
        <?php foreach ($game_servers as $gameserver): ?>
            <li draggable="true" data-gameserver-id="<?= $gameserver['gs_id'] ?>"
                data-status="<?= GameServerStatus::$STYLE_TEXT[$gameserver['online_status']] ?>">
                <div class="drag-hook">&nbsp;</div>
                <a class="delete-box" href="">&nbsp;</a>
                <h1><?= $gameserver['nickname'] ?></h1>
                <h2><?= $gameserver['game_name'] ?></h2>
            <span class="actions-and-status">
                    <span class="status-ip">
                        <?= $gameserver['ip'] ?>:<?= $gameserver['port'] ?>
                        <a class="server-connect" href="#">&nbsp;</a>
                    </span>
            </span>

            <span class="actions-and-status">
                <span class="gs-status">&nbsp;</span>
            </span>

            <span class="actions-and-status actions">


                <a class="server-action" data-action="start" href="#/server/<?= $gameserver['gs_id'] ?>/start">
                    Start
                </a>

                <a class="server-action" data-action="restart" href="#/server/<?= $gameserver['gs_id'] ?>/restart">
                    Restart
                </a>

                <a class="server-action" data-action="stop" href="#/server/<?= $gameserver['gs_id'] ?>/stop">
                    Stop
                </a>

                <a class="server-action manage-server" href="/dashboard/manage/<?= $gameserver['gs_id'] ?>">
                    Manage
                </a>
            </span>
            </li>
        <?php endforeach; ?>
        <!-- End Server Box Template -->

    </ul>
</div>