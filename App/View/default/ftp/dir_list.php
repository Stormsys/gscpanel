<div class="current-path">
    Current Path:<a href="#" class="ftp-link" data-path="">/</a>

    <?php
    $full_path = '';
    foreach (explode('/', $cur_path) as $path_part) {
        if (!empty($path_part)) {
            $full_path .= '/' . $path_part;
            ?>
            <a href="#" class="ftp-link" data-path="<?= $full_path ?>"><?= $path_part ?>/</a>
            <?php
        }
    }
    ?>
</div>
<table class='file-manager'>
    <thead>
    <tr>
        <td width="20px"></td>
        <td>Filename</td>
        <td width="200px" class="right">Date Modified</td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr data-path='<?= $item->fullpath ?>/' data-type='<?= $item->type ?>'>
            <td><img src="/assets/img/icons/<?= $item->type ?>.png" alt="<?= $item->type ?>"/></td>
            <td><?= $item->filename ?></td>
            <td class="right"><?php if ($item->type != 'dir') echo $item->filetime; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>