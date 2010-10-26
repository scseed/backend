<?php defined('SYSPATH') or die('No direct access allowed.');?>
<table>
	<tr>
		<?php foreach($meta->fields() as $field): if($field->in_table):?>
			<th><?php echo $field->name?></th>
		<?php endif; endforeach;?>
			<th>Операции</th>
	</tr>
<?php foreach($items as $item):?>
	<tr class="<?php echo ($item->is_active) ? 'success' : 'error'?>">
	<?php foreach($meta->fields() as $field): if($field->in_table):?>
		<?php if(! is_object($item->{$field->name})):?>
			<td class="name"><?php echo $item->{$field->name}?></td>
		<?php endif;?>
		<?php if(is_object($item->{$field->name}) AND !( $item->{$field->name} instanceof Jelly_Collection)):?>
			<td class="name"><?php echo $item->{$field->name}->name?></td>
		<?php endif;?>
		<?php if(is_object($item->{$field->name}) AND  $item->{$field->name} instanceof Jelly_Collection):?>
			<td class="name">
			<?php foreach($item->{$field->name} as $_item):?>
				<?php echo $_item->name?>,<br />
			<?php endforeach;?>
			</td>
			<?php endif;?>
		<?php endif; endforeach;?>
		<td class="edit">
			<?php echo HTML::anchor(
				'admin/' . $meta->model() . '/edit/' . $item->id,
				HTML::image('admin/media/i/icons/edit.png', array('class' => 'ico')),
				array(
					'title' => 'Редактировать'
				))?>
			<?php echo HTML::anchor(
				'admin/' . $meta->model() . '/status/' . $item->id,
				($item->is_active)
					? HTML::image('admin/media/i/icons/cross.png', array('class' => 'ico'))
					: HTML::image('admin/media/i/icons/tick.png', array('class' => 'ico')),
				array(
					'title' => 'Сменить статус'
				))?>
			<?php echo HTML::anchor(
				'admin/' . $meta->model() . '/delete/' . $item->id,
				HTML::image('admin/media/i/icons/trash.png', array('class' => 'ico')),
				array(
					'title' => 'Удалить'
				))?>
		</td>
	</tr>
<?php endforeach;?>
</table>
