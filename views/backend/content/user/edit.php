<?php defined('SYSPATH') OR die('No direct access allowed.');?>

<div class="edit-content-form">
<?php echo Form::open(); ?>
<?php foreach ($fields as $field): if($field->in_form):?>
	<div class="form-item">
		<?php echo Form::label($field->name, $field->label)?>
		
		<?php if (isset($errors[$field->name])): ?>
			<div class="form-error"><?php echo $errors[$field->name] ?></div>
		<?php endif?>

		<?php
			if($field->name == 'password')
				$user->set($field->name, '');

			echo $user->input($field->name);
		?>
	</div>
<?php endif; endforeach;?>
	<div class="form-item text-right">
			<?php
				echo Html::anchor(
								Route::get('admin')
										->uri(array(
												'controller' => 'user',
												'action' => 'list',
								)),
								 __('Отмена'),
								array('class' => 'button', 'title' => __('Cancel'))
				);
			?>
		<?php
			echo Form::button(
							'save',
							__('Сохранить'),
							array ('type' => 'submit')
			);
		?>
	</div>
<?php  echo Form::close(); ?>
</div>