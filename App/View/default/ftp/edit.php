<div class="current-path">
	Current Path:<a href="#" class="ftp-link" data-path="">/</a>

	<?php
	$full_path = '';
	$i = 1;
	$total = count(explode( '/', $cur_path));
	foreach(explode( '/', $cur_path) as $path_part)
	{
		$i++;
		if(!empty($path_part))
		{
			$full_path .= '/' . $path_part;
			if($i < $total)
			{
			?>

			<a href="#" class="ftp-link" data-path="<?=$full_path?>"><?=$path_part?>/</a>
	<?php
			}
			else
			{
				?>

				<a href="#"><?=$path_part?>/</a>
	<?php
			}
		}
	}
	?>
</div>
<textarea rows="20" id="ftp-edit-box"><?=$content?></textarea>
<input type="button" id="ftp-save" data-save-path="<?=$cur_path?>" value="Save Changes" />