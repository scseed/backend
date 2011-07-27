<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<?php echo ($parent_lvl_id)
		? HTML::anchor(
			Route::url('admin', array('controller' => 'page', 'action' => 'list')).URL::query(array('parent' => $parent_lvl_id)),
			__('На уровень выше')
		 )
		: NULL;
?>
<table cellspacing="0">
	<tr>
		<th>&uarr;/&darr;</th>
		<th>Наименование</th>
		<th>алиас</th>
		<th>Языки содержания</th>
		<th>Операции</th>
	</tr>
<?php foreach($pages as $page): ?>
	<tr>
		<td>
			<?php echo HTML::anchor(
				Request::current()->uri(array(
					'controller' => 'page',
					'action' => 'move',
					'id' => $page['id'],
				)) . URL::query(array('direction' => 'up')),
				'&uarr; выше',
				array('title' => __('переместить на пункт выше')))?>
			<?php echo HTML::anchor(
				Request::current()->uri(array(
					'controller' => 'page',
					'action' => 'move',
					'id' => $page['id'],
				)) . URL::query(array('direction' => 'down')),
				'&darr; ниже',
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
<!--			--><?php //$all=count($page['page_contents']); $i=1; foreach($page['page_contents'] as $content)
//			{
//			  echo $content->lang->abbr;
//			  if($i < $all) echo ', ';
//			  $i++;
//			}?>
		</td>
		<td><?php
				echo html::image('admin/media/i/icons/edit.png', array('class'=>'ico 16x16'));
				echo html::anchor('admin/page/edit/'.$page['id'], 'Править');
				echo '&nbsp;&nbsp;';
				echo html::image('admin/media/i/icons/trash.png', array('class'=>'ico 16x16'));
				echo html::anchor('admin/page/delete/'.$page['id'], 'Удалить',
					array('onclick'=>"return window.confirm('Уверены в этом?')"));
				echo '&nbsp;&nbsp;';
				echo html::image('admin/media/i/icons/add.png', array('class'=>'ico 16x16'));
				echo HTML::anchor(
					Route::url('admin', array('controller' => 'page', 'action' => 'add')).URL::query(array('parent' => $page['id'])),
					__('добавить дочерний документ')
				);
			?>
		</td>
	</tr>
<?php endforeach;?>
</table>

<?php
	echo html::image('admin/media/i/icons/add.png', array('class'=>'ico 16x16'));
	echo HTML::anchor(
	Route::url('admin', array('controller' => 'page', 'action' => 'add')).URL::query(),
	__('Добавить новую страницу на этом уровне')
)?>