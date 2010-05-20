<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<?php //echo Kohana::debug($users)?>
<table cellspacing="0">
	<tr>
<?php
foreach($meta->fields() as $field):
	if($field->in_table):
?>
		<th><?php echo $field->label ?></th>
<?php endif; endforeach;?>
		<th>Операции</th>
	</tr>
<?php foreach($users as $user):?>
	<tr>
<?php foreach($meta->fields() as $field): if($field->in_table):?>
		<td>
<?php
switch($field)
{
	case $field instanceof Jelly_Field_Boolean:
		$label = ($user->{$field->name}) ? 'label_true' : 'label_false';
		echo $field->$label;
		break;
	case $field instanceof Jelly_Field_ManyToMany:
		$i = 0; $coma = '';
		$count = count($user->{$field->name});
		foreach ($user->{$field->name} as $param)
		{
			if(++$i < $count)
				$coma = ', ';
			echo $param->name . $coma;
		}
		break;
	default:
		echo $user->{$field->name};
		break;
		
}?>
		</td>
<?php endif; endforeach;?>
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