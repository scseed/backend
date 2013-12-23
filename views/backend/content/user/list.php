<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<?php echo HTML::anchor(
	Route::url('admin', array('controller' => 'user', 'action' => 'add')),
	'<i class="icon-plus"></i> Добавить пользователя',
	array('class' => 'btn btn-primary pull-right', 'id' => 'addBtn'))?>
<?php echo $pagination?>
<table class="table table-striped table-bordered table-hover">
	<thead>
	<tr>
		<th>Фамилия</th>
		<th>Имя</th>
		<th>Отчество</th>
		<th>email</th>
		<th>Дата последнего входа</th>
		<th>Количество заходов</th>
		<th>Статус</th>
		<th></th>
	</tr>
	</thead>
<?php foreach($users as $role_user):?>
	<tr>
		<td><?php echo $role_user->user->last_name?></td>
		<td><?php echo $role_user->user->first_name?></td>
		<td><?php echo $role_user->user->patronymic?></td>
		<td><?php echo $role_user->user->email?></td>
		<td><?php echo $role_user->user->last_login?></td>
		<td><?php echo $role_user->user->logins?></td>
		<td><?php echo ($role_user->user->is_active) ? 'Активен' : 'Отключён'?></td>
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
							Route::url('admin', array('controller' => 'user', 'action' => 'activity', 'id' => $role_user->user->id)),
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
<?php echo $pagination?>