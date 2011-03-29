<?php defined('SYSPATH') or die('No direct access allowed.');?>
<div class="edit-content-form">
	<?php echo Form::open(Request::current());?>
	<?php echo Form::hidden('page_id', $page->id)?>
	<div class="form-item">
<?php
		echo Form::label('alias', 'Алиас');
		if (isset($errors['alias'])):
?>
			<div class="form-error"><?php echo $errors['alias'] ?></div>
<?php
		endif;
		echo Form::input('alias', $alias);
?>
	</div>
	<div class="form-item">
<?php
		echo Form::label('parent', 'Родительский Элемент');
		if (isset($errors['parent'])):
?>
			<div class="form-error"><?php echo $errors['parent'] ?></div>
<?php
		endif;
		echo Form::select('parent', $pages, $parent->id);
?>
	</div>
	<div class="form-item">
<?php
		echo Form::label('is_active', 'Опубликовано');
		if (isset($errors['is_active'])):
?>
			<div class="form-error"><?php echo $errors['is_active'] ?></div>
<?php
		endif;
		echo Form::select('is_active', array(true => 'да', false => 'нет'), $page->is_active);
?>
	</div>
	<fieldset><legend>Содержание на русском языке</legend>
		<div class="form-item">
	<?php
			echo Form::label('ru[title]', 'Заголовок');
			if (isset($errors['ru[title]'])):
	?>
				<div class="form-error"><?php echo $errors['ru[title]'] ?></div>
	<?php
			endif;
			echo Form::input('ru[title]', $ru_content->title);
	?>
		</div>
		<div class="form-item">
	<?php
			echo Form::label('ru[long_title]', 'Заголовок на странице');
			if (isset($errors['ru[long_title]'])):
	?>
				<div class="form-error"><?php echo $errors['ru[long_title]'] ?></div>
	<?php
			endif;
			echo Form::input('ru[long_title]', $ru_content->long_title);
	?>
		</div>
		<div class="form-item">
	<?php
			echo Form::label('ru[content]', 'Содержание');
			if (isset($errors['ru[content]'])):
	?>
				<div class="form-error"><?php echo $errors['ru[content]'] ?></div>
	<?php
			endif;
			echo Form::textarea('ru[content]', $ru_content->content);
	?>
		</div>
	</fieldset>
	<fieldset><legend>Содержание на английском языке</legend>
		<div class="form-item">
	<?php
			echo Form::label('en[title]', 'Заголовок');
			if (isset($errors['en[title]'])):
	?>
				<div class="form-error"><?php echo $errors['en[title]'] ?></div>
	<?php
			endif;
			echo Form::input('en[title]', $en_content->title);
	?>
		</div>
		<div class="form-item">
	<?php
			echo Form::label('en[long_title]', 'Заголовок на странице');
			if (isset($errors['en[long_title]'])):
	?>
				<div class="form-error"><?php echo $errors['en[long_title]'] ?></div>
	<?php
			endif;
			echo Form::input('en[long_title]', $en_content->long_title);
	?>
		</div>
		<div class="form-item">
	<?php
			echo Form::label('en[content]', 'Содержание');
			if (isset($errors['en[content]'])):
	?>
				<div class="form-error"><?php echo $errors['en[content]'] ?></div>
	<?php
			endif;
			echo Form::textarea('en[content]', $en_content->content);
	?>
		</div>
	</fieldset>
	<div class="form-item text-right">
		<?php echo Html::anchor(
			Route::get('admin')
				->uri(array(
					'controller' => $controller,
					'action' => 'list',
			)),
			 __('Отмена'),
			array('class' => 'button', 'title' => __('Отмена')));?>
		<?php echo Form::button(
			'save',
			__('Сохранить'),
			array ('type' => 'submit'));?>
	</div>
	<?php echo Form::close();?>
</div>