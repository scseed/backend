<?php defined('SYSPATH') or die('No direct access allowed.');?>
<div class="edit-content-form">
<?php echo Form::open();?>
<?php foreach ($item->meta()->fields() as $field): if($field->in_form):?>
	<div class="form-item">
		<?php echo Form::label($field->name, $field->label)?>

		<?php if (isset($errors[$field->name])): ?>
			<div class="form-error"><?php echo $errors[$field->name] ?></div>
		<?php endif?>

		<?php echo $item->input($field->name);?>
	</div>
<?php endif; endforeach;?>
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
<?php  echo Form::close(); ?>
</div>