<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<table cellspacing="0">
	<tr>
		<th>Наименование</th>
		<th>алиас</th>
		<th>Языки содержания</th>
		<th>Операции</th>
	</tr>
<?php foreach($pages as $page_content): ?>
	<tr>
		<td>
			<?php echo $page_content->title?>
			<?php $childrens = $page_content->page->children(); if(count($childrens)):?>
			[<?php echo HTML::anchor(Request::current()->uri().URL::query(array('parent' => $page_content->page->id)), 'вложённые документы')?>]
			<ul class="small">
				<?php foreach($childrens as $child_page):?>
				<li><?php echo $child_page->get('page_contents')->where('lang', '=', 1)->limit(1)->execute()->title?></li>
				<?php endforeach;?>
			</ul>
			<?php endif;?>
		</td>
		<td><?php echo $page_content->page->alias?></td>
		<td><?php $all=count($page_content->page->page_contents); $i=1; foreach($page_content->page->page_contents as $content)
			{
			  echo $content->lang->abbr;
			  if($i < $all) echo ', ';
			  $i++;
			}?></td>
		<td><?php
				echo html::image('admin/media/i/icons/edit.png', array('class'=>'ico 16x16'));
				echo html::anchor('admin/page/edit/'.$page_content->page->id, 'Править');
				echo '&nbsp;&nbsp;';
				echo html::image('admin/media/i/icons/add.png', array('class'=>'ico 16x16'));
				echo html::anchor('admin/page/add/'.URL::query(array('parent' => $page_content->page->id)), 'создать здесь');
				echo '&nbsp;&nbsp;';
				echo html::image('admin/media/i/icons/trash.png', array('class'=>'ico 16x16'));
				echo html::anchor('admin/page/delete/'.$page_content->page->id, 'Удалить',
					array('onclick'=>"return window.confirm('Уверены в этом?')"));?>
		</td>
	</tr>
<?php endforeach;?>
</table>