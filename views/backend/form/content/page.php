<?php defined('SYSPATH') or die('No direct access allowed.');?>
<div class="edit-content-form">
	<?php echo Form::open(Request::current());?>
	<?php echo Form::hidden('page_id', $page->id)?>

	<div class="form-item">
	<?php echo Form::label('alias', 'Алиас');?>
	<?php if (isset($errors['alias'])):?>
		<div class="form-error"><?php echo $errors['alias'] ?></div>
	<?php endif;?>
	<?php echo ($parent AND $parent->alias) ? $parent->alias . '/ ' : NULL;?><?php echo Form::input('alias', $alias);?>
	</div>

	<div class="form-item">
	<?php echo Form::label('parent_page', 'Родительский Элемент');?>
	<?php if (isset($errors['parent_page'])):?>
		<div class="form-error"><?php echo $errors['parent_page'] ?></div>
	<?php endif;?>
	<?php echo Form::select('parent_page', $pages, ($parent) ? $parent->id : 0);?>
	</div>

	<div class="form-item">
	<?php echo Form::label('type', 'Тип страницы');?>
	<?php if (isset($errors['type'])):?>
		<div class="form-error"><?php echo $errors['type'] ?></div>
	<?php endif;?>
	<?php echo Form::select('type', $page_types, NULL);?>
	</div>

	<div class="form-item">
	<?php echo Form::label('is_active', 'Опубликовано');?>
	<?php if (isset($errors['is_active'])):?>
		<div class="form-error"><?php echo $errors['is_active'] ?></div>
	<?php endif;?>
	<?php echo Form::select('is_active', array(true => 'да', false => 'нет'), $page->is_active);?>
	</div>

	<div class="form-item">
	<?php echo Form::label('is_visible', 'Доступно для просмотра');?>
	<?php if (isset($errors['is_visible'])):?>
		<div class="form-error"><?php echo $errors['is_visible'] ?></div>
	<?php endif;?>
	<?php echo Form::select('is_visible', array(true => 'да', false => 'нет'), $page->is_visible);?>
	</div>

<?php foreach($content as $abbr => $content):?>
	<?php echo Form::hidden($abbr . 'content_id', $content['content']->id)?>
	<fieldset class="page_content<?php echo ( ! $content['content']->title) ? ' closed' : NULL?>">
		<legend>Содержание. Язык: <?php echo $content['lang']->locale_name?></legend>
		<div class="form-item">
			<?php echo Form::label($abbr . '[title]', 'Заголовок');?>
			<?php if (isset($errors[$abbr]['title'])):?>
				<div class="form-error"><?php echo $errors[$abbr]['title'] ?></div>
			<?php endif;?>
			<?php echo Form::input($abbr . '[title]', $content['content']->title);?>
		</div>
		<div class="form-item">
		<?php echo Form::label($abbr . '[long_title]', 'Заголовок на странице');?>
		<?php if (isset($errors[$abbr]['long_title'])):?>
			<div class="form-error"><?php echo $errors[$abbr]['long_title'] ?></div>
		<?php endif;?>
		<?php echo Form::input($abbr . '[long_title]', $content['content']->long_title);?>
		</div>
		<div class="form-item">
		<?php echo Form::label($abbr . '[content]', 'Содержание');?>
		<?php if (isset($errors[$abbr]['content'])):?>
			<div class="form-error"><?php echo $errors[$abbr]['content'] ?></div>
		<?php endif;?>
		<?php echo Form::textarea($abbr . '[content]', $content['content']->content, array('id' => $abbr));?>
		</div>
		<?php if($content['content']->id)
			{
				echo HTML::anchor(
					Route::url('admin_ajax', array('controller' => 'page_content', 'action' => 'delete', 'id' => $content['content']->id)),
					HTML::image('admin/media/i/icons/trash.png', array('class'=>'ico 16x16')).
					' Удалить языковое содержание',
					array('class' => 'delete_content')
				);
			}
		?>
	</fieldset>
<?php endforeach;?>

	<div class="form-item text-right">
		<?php echo Html::anchor(
			Route::get('admin')
				->uri(array(
					'controller' => 'page',
					'action' => 'list',
			)),
			 __('Отмена'),
			array('class' => 'button', 'title' => __('Отмена')));?>
		<?php echo Form::button(NULL, __('Сохранить'), array ('type' => 'submit'));?>
	</div>
	<?php echo Form::close();?>
</div>