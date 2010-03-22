<?php defined('SYSPATH') OR die('No direct access allowed.');?>

<div class="edit-content-form">
<?php echo Form::open(); ?>
<?php foreach ($fields as $field): if($field->in_form):?>
	<div class="form-item">
		<?php
			echo Form::label($field->name, $field->label);
		?>
		<?php if (isset($errors[$field->name])): ?>
			<div class="form-error"><?php echo $errors[$field->name] ?></div>
		<?php endif ?>
		<?php
			echo Form::input(
							$field->name,
							(isset($_POST[$field->name])) ? $_POST[$field->name] : '',
							array('type' => 'text', 'id' => 'email')
			);
		?>
	</div>
<?php endif; endforeach;/*?>
	<div class="form-item">
		<?php
			echo Form::label('first_name', __('Имя'));
		?>
		<?php if (!empty($errors['first_name'])): ?>
			<div class="form-error"><?php echo $errors['first_name'] ?></div>
		<?php endif ?>
		<?php
			echo Form::input(
							'first_name',
							$userdata['first_name'],
							array('type' => 'text', 'id' => 'first_name')
			);
		?>
	</div>
	<div class="form-item">
		<?php
			echo Form::label('last_name', __('Фамилия'));
		?>
		<?php if (!empty($errors['last_name'])): ?>
			<div class="form-error"><?php echo $errors['last_name'] ?></div>
		<?php endif ?>
		<?php
			echo Form::input(
							'last_name',
							$userdata['last_name'],
							array('type' => 'text', 'id' => 'last_name')
			);
		?>
	</div>
	<div class="form-item">
		<?php
			echo Form::label('patronymic', __('Отчество'));
		?>
		<?php
			echo Form::input(
							'patronymic',
							$userdata['patronymic'],
							array('type' => 'text', 'id' => 'patronymic')
			);
		?>
	</div>
	<div class="form-item">
		<?php
			echo Form::label('password', __('Пароль'));
		?>
		<?php if (!empty($errors['password'])): ?>
			<div class="form-error"><?php echo $errors['password'] ?></div>
		<?php endif ?>
		<?php
			echo Form::input(
							'password',
							'',
							array('type' => 'password', 'id' => 'password')
			);
		?>
	</div>
	<div class="form-item">
		<?php
			echo Form::label('password_confirm', __('Повторите пароль'));
		?>
		<?php if (!empty($errors['password_confirm'])): ?>
			<div class="form-error"><?php echo $errors['password_confirm'] ?></div>
		<?php endif ?>
		<?php
			echo Form::input(
							'password_confirm',
							'',
							array('type' => 'password', 'id' => 'password_confirm')
			);
		?>
	</div>
	<div class="form-item">
		<?php
			echo Form::label('roles', __('Роль'));
		?>
		<?php
			echo Form::select(
							'roles',
							$roles,
							! empty($userdata['roles']) ? $userdata['roles'] : '',
							array('id' => 'roles')
			);
		?>
	</div><?php */?>
	<div class="form-item text-right">
		<?php /*if ($edit): ?>
			<?php
				echo Html::anchor(
								Route::get('admin')
										->uri(array(
												'controller' => 'user',
												'action' => '',
								)),
								 __('Cancel'),
								array('class' => 'button', 'title' => __('Cancel'))
				);
			?>
		<?php endif */?>
		<?php
			echo Form::button(
							'save',
							//! $edit ?
								__('Add'), //:
								//__('Save'),
							array ('type' => 'submit')
			);
		?>
	</div>
	<?php //echo Form::hidden('edit', $edit) ?>
<?php  echo Form::close(); ?>
</div>