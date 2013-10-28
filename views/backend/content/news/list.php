<?php defined('SYSPATH') or die('No direct access allowed.');?>
<?php echo HTML::anchor(
	Route::url('admin', array('controller' => 'news', 'action' => 'add')),
	'<i class="icon-plus"></i> Добавить новость',
	array('class' => 'btn btn-primary pull-right', 'id' => 'addNewsBtn'))?>
<?php echo $pagination?>
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>Заголовок новости</th>
			<th>Публикация</th>
			<th>Статус</th>
			<th></th>
		</tr>
	</thead>
<?php foreach($news as $article):
	$status_title = ($article->is_active) ? 'Активная новость' : 'Неактивная новость';
	$status_icon  = ($article->is_active) ? 'ok-circle' : 'ban-circle';
	$status_color = ($article->is_active) ? 'green' : 'red';
	?>
	<tr class="<?php echo (!$article->is_active) ? 'error' : NULL?>">
		<td><?php echo $article->title?></td>
		<td><?php echo date("d.m.Y", $article->pubdate)?></td>
		<td><i rel="tooltip" data-title="<?php echo $status_title?>" data-placement="bottom" class="icon-<?php echo $status_icon?> icon-<?php echo $status_color?>"></i></td>
		<td>
			<div class="btn-group">
				<a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
					<i class="icon-cog"></i>
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu pull-right">
					<li>
						<?php echo HTML::anchor(
							'admin/news/edit/' . $article->id,
							'<i class="icon-edit"></i> Редактировать')?>
					</li>
					<li>
						<?php echo HTML::anchor(
							'admin/news/status/'.$article->id,
							($article->is_active) ? '<i class="icon-ban-circle"></i> Выключить' : ' <i class="icon-ok-circle"></i> Включить'
						)?>
					</li>
				</ul>
			</div>

		</td>
	</tr>
<?php endforeach;?>
</table>
<?php echo $pagination?>
