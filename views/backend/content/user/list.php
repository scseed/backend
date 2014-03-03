<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<table class="table table-striped table-bordered table-hover">
	<thead>
	<tr>
<?php
foreach($meta->fields() as $field):
	if($field->in_table):
?>
		<th><?php echo $field->label ?></th>
<?php endif; endforeach;?>
		<th></th>
	</tr>
	</thead>
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
		echo implode($role_user->user->{$field->name}->as_array('id', 'description'), ', ');
//			echo $role_user->role->description;
		break;
	default:
		echo $role_user->user->{$field->name};
		break;

}?>
		</td>
<?php endif; endforeach;?>
		<td>
			<div class="btn-group">
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
					<i class="icon-cog"></i>
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu pull-right">
					<li>
						<?php echo HTML::anchor(
							Route::url('admin', array('controller' => 'user', 'action' => 'edit', 'id' => $role_user->user->id)),
							'<i class="icon-edit"></i> '.__('Править')
						)?>
					</li>
					<li>
						<?php echo HTML::anchor(
							Route::url('admin', array('controller' => 'user', 'action' => 'delete', 'id' => $role_user->user->id)),
							'<i class="icon-trash"></i> '.__('Удалить'),
							array('onclick'=>"return window.confirm('Уверены в этом?')")
						)?>
					</li>
				</ul>
			</div>
		</td>
	</tr>
<?php endforeach;?>
</table>