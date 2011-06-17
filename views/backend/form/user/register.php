<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<div id="login-form">
	<h1 class="title"><?php echo __('Регистрация администратора') ?></h1>
		<?php echo Form::open(Request::current()); ?>
			<div class="form-item">
				<?php
					echo Form::label('email', __('Эл. адрес'));
				?>
				<?php if (!empty($errors['email'])): ?>
					<div class="form-error"><?php echo $errors['email'] ?></div>
				<?php endif ?>
				<?php
					echo Form::input(
									'email',
									$userdata->email,
									array('type' => 'text', 'id' => 'email')
					);
				?>
			</div>
			<div class="form-item">
				<?php
					echo Form::label('password', __('Пароль'));
				?>
				<?php if ( ! empty($userdata->password) AND ! empty($errors['password'])): ?>
					<div class="form-error"><?php echo $errors['password'] ?></div>
				<?php endif ?>
				<?php
					echo Form::input(
									'password',
									'',
									array('type' => 'password', 'id' => 'password')
					);
				?>
			</div><div class="form-item">
				<?php
					echo Form::label('password_confirm', __('Подтверждение пароля'));
				?>
				<?php if ( ! empty($userdata->password_confirm) AND ! empty($errors['password_confirm'])): ?>
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
					echo Form::label('remember', __('Запомнить меня'));
				?>
				<?php
					echo Form::checkbox(
									'remember',
									'1',
									!empty($_POST['remember']) ? TRUE : FALSE,
									array('id' => 'remember')
					);
				?>
			</div>
			<div class="form-item text-right">
				<?php
					echo Form::button(
									'login',
									'Войти &raquo;',
									array ('type' => 'submit')
					);
				?>
			</div>
		<?php echo Form::close(); ?>
</div>