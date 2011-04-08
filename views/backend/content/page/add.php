<?php defined('SYSPATH') or die('No direct access allowed.');?>
<div class="edit-content-form">
	<?php echo Form::open(Request::current());?>
	<?php echo Form::hidden('page_id', $page->id)?>

	<div class="form-item">
	<?php echo Form::label('alias', 'Алиас');?>
	<?php if (isset($errors['alias'])):?>
		<div class="form-error"><?php echo $errors['alias'] ?></div>
	<?php endif;?>
	<?php echo ($parent->alias) ? $parent->alias . '/ ' : NULL;?><?php echo Form::input('alias', $alias);?>
	</div>

	<div class="form-item">
	<?php echo Form::label('parent', 'Родительский Элемент');?>
	<?php if (isset($errors['parent'])):?>
		<div class="form-error"><?php echo $errors['parent'] ?></div>
	<?php endif;?>
	<?php echo Form::select('parent', $pages, $parent->id);?>
	</div>

	<div class="form-item">
	<?php echo Form::label('is_active', 'Опубликовано');?>
	<?php if (isset($errors['is_active'])):?>
		<div class="form-error"><?php echo $errors['is_active'] ?></div>
	<?php endif;?>
	<?php echo Form::select('is_active', array(true => 'да', false => 'нет'), $page->is_active);?>
	</div>
	
<?php foreach($content as $abbr => $content):?>
	<?php echo Form::hidden($abbr . 'content_id', $content['content']->id)?>
	<fieldset><legend>Содержание. Язык: <?php echo $content['lang']->locale_name?></legend>
		<div class="form-item">
			<?php echo Form::label($abbr . '[title]', 'Заголовок');?>
			<?php if (isset($errors['ru[title]'])):?>
				<div class="form-error"><?php echo $errors[$abbr]['title'] ?></div>
			<?php endif;?>
			<?php echo Form::input($abbr . '[title]', $content['content']->title);?>
		</div>
		<div class="form-item">
		<?php echo Form::label($abbr . '[long_title]', 'Заголовок на странице');?>
		<?php if (isset($errors['ru[long_title]'])):?>
			<div class="form-error"><?php echo $errors[$abbr]['long_title'] ?></div>
		<?php endif;?>
		<?php echo Form::input($abbr . '[long_title]', $content['content']->long_title);?>
		</div>
		<div class="form-item">
		<?php echo Form::label($abbr . '[content]', 'Содержание');?>
		<?php if (isset($errors[$abbr . '[content]'])):?>
			<div class="form-error"><?php echo $errors[$abbr]['content'] ?></div>
		<?php endif;?>
		<?php echo Form::textarea($abbr . '[content]', $content['content']->content);?>
		</div>
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