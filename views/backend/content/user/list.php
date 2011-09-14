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
<?php foreach($users as $role_user):?>
	<tr>
<?php foreach($meta->fields() as $field): if($field->in_table):?>
		<td>
<?php
switch($field)
{
	case $field instanceof Jelly_Field_Boolean:
		$label = ($role_user->user->{$field->name}) ? 'label_true' : 'label_false';
		echo $field->$label;
		break;
	case $field instanceof Jelly_Field_Timestamp:
		echo date('d.m.Y', $role_user->user->{$field->name});
		break;
	case $field instanceof Jelly_Field_ManyToMany:
//		echo implode($role_user->user->{$field->name}->as_array('id', 'description'), ', ');
			echo $role_user->role->description;
		break;
	default:
		echo $role_user->user->{$field->name};
		break;

}?>
		</td>
<?php endif; endforeach;?>
		<td>
			<?php echo HTML::anchor(
				Route::url('admin', array('controller' => 'user', 'action' => 'edit', 'id' => $role_user->user->id)),
				HTML::image('admin/media/i/icons/user--pencil.png', array('alt' => __('Править'))),
				array('title' => __('Править'))
			)?>&nbsp;&nbsp;
			<?php echo HTML::anchor(
				Route::url('admin', array('controller' => 'user', 'action' => 'delete', 'id' => $role_user->user->id)),
				HTML::image('admin/media/i/icons/user--minus.png', array('alt' => __('Удалить'))),
				array('title' => __('Удалить'), 'onclick'=>"return window.confirm('Уверены в этом?')")
			)?>
		</td>
	</tr>
<?php endforeach;?>
</table>