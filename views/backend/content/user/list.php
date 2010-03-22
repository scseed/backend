<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<?php //echo Kohana::debug($users)?>
<table cellspacing="0">
	<tr>
<?php
foreach($users_meta->fields() as $meta):
	if($meta->in_table):
?>
		<th><?php echo $meta->label ?></th>
<?php endif; endforeach;?>
		<th>Операции</th>
	</tr>
<?php foreach($users as $user):?>
	<tr>
		<td><?php echo $user->id?></td>
		<td><?php echo $user->email?></td>
		<td><?php echo $user->name?></td>

		<td><?php echo ($user->last_login) ? date('H:i l d M Y', $user->last_login) : 'никогда'?></td>
		<td>
			<?php
				echo html::image('admin_i/icons/edit.png', array('class'=>'ico 16x16'));
				echo html::anchor('admin/user/edit/'.$user->id, 'Править');
				echo '&nbsp;&nbsp;';
				echo html::image('admin_i/icons/user.png', array('class'=>'ico 16x16'));
				echo html::anchor('admin/user/roles/'.$user->id, 'Роли');
				echo '&nbsp;&nbsp;';
				echo html::image('admin_i/icons/trash.png', array('class'=>'ico 16x16'));
				echo html::anchor('admin/user/delete/'.$user->id, 'Удалить',
					array('onclick'=>"return window.confirm('Уверены в этом?')")); 
			?>
		</td>
	</tr>
<?php endforeach;?>
</table>