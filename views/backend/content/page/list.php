<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<?php echo ($parent_lvl instanceof Jelly_Model AND $parent_lvl->loaded() AND $parent_lvl->parent_page->id != NULL)
		? '<p>'.HTML::anchor(
			Route::url('admin', array('controller' => 'page', 'action' => 'list')).URL::query(array('parent' => $parent_lvl->parent_page->id)),
			'<i class="icon-reply"></i> '.__('На уровень выше'),
			array('class' => 'btn btn-small btn-inverse')
		 ).'</p>'
		: NULL;
?>
<table class="table table-striped table-bordered table-hover table-condensed">
	<thead>
	<tr>
		<th>Позиция</th>
		<th>Наименование</th>
		<th>алиас</th>
		<th>Языки содержания</th>
		<th>Операции</th>
	</tr>
	</thead>
<?php foreach($pages as $page): ?>
	<tr>
		<td>
			<?php echo HTML::anchor(
				Request::current()->route()->uri(array(
					'controller' => 'page',
					'action' => 'move',
					'id' => $page['id'],
				)) . URL::query(array('direction' => 'up')),
				'<i class="icon-arrow-up"></i>',
				array('title' => __('переместить на пункт выше')))?>&nbsp;
			<?php echo HTML::anchor(
				Request::current()->route()->uri(array(
					'controller' => 'page',
					'action' => 'move',
					'id' => $page['id'],
				)) . URL::query(array('direction' => 'down')),
				'<i class="icon-arrow-down"></i>',
				array('title' => __('переместить на пункт ниже')))?>
		</td>
		<td>
			<?php echo $page['title']?>
			<?php $childrens = $page['childrens']; if(count($childrens)):?>
			[<?php echo HTML::anchor(Request::current()->uri().URL::query(array('parent' => $page['id'])), 'вложённые документы')?>]
			<ul class="small">
				<?php foreach($childrens as $child_page):?>
				<li><?php echo $child_page['title']?></li>
				<?php endforeach;?>
			</ul>
			<?php endif;?>
		</td>
		<td><?php echo $page['alias']?></td>
		<td>
			<?php echo implode(', ', $page['langs'])?>
		</td>
		<td>
			<div class="btn-group">
				<a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
					<i class="icon-cog"></i>
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu pull-right">
					<li>
						<?php echo HTML::anchor(
							Route::url('admin', array('controller' => 'page', 'action' => 'edit', 'id' => $page['id'])),
							'<i class="icon-edit"></i> Редактировать'
						); ?>
					</li>
					<li>
						<?php echo HTML::anchor(
							Route::url('admin', array('controller' => 'page', 'action' => 'add')).URL::query(array('parent' => $page['id'])),
							'<i class="icon-plus"></i> добавить дочерний документ'
						);?>
					</li>
					<li>
						<?php echo HTML::anchor(
							Route::url('admin', array('controller' => 'page', 'action' => 'delete', 'id' => $page['id'])),
							'<i class="icon-trash"></i> Удалить',
							array('onclick'=>"return window.confirm('Уверены в этом?')")
						);?>
					</li>
				</ul>
			</div>
		</td>
	</tr>
<?php endforeach;?>
</table>

<?php
	echo HTML::anchor(
		Route::url('admin', array('controller' => 'page', 'action' => 'add')).URL::query(array('parent' => $parent_lvl->id)),
		'<i class="icon-plus"></i> ' . __('Добавить новую страницу на этом уровне'),
		array('class' => 'btn btn-primary btn-small')
)?>