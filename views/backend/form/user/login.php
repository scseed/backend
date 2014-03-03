<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<div class="row-fluid" style="margin: 5em auto">
	<div class="span3">&nbsp;</div>
	<div class="span6">
		<?php echo Form::open(Request::current(), array('class' => 'form-horizontal')); ?>
		<fieldset>
			<legend><?php echo __('Авторизуйтесь');?></legend>
			<?php if (!empty($errors)): ?>
				<div class="alert alert-error">
					<div><?php echo (is_array($errors)) ? implode(', ', $errors) : $errors?></div>
				</div>
			<?php endif ?>
			<div class="control-group">
				<?php echo Form::label('login_email', __('Email'), array('class' => 'control-label'))?>
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-envelope"></i></span>
						<?php echo Form::input(
							'email',
							Arr::get($post, 'email'),
							array('type' => 'text', 'id' => 'login_email', 'class' => 'input-xlarge')
						);?>
					</div>
				</div>
			</div>
			<div class="control-group">
				<?php echo Form::label('login_password', __('Пароль'), array('class' => 'control-label'))?>
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-asterisk"></i></span>
						<?php echo Form::input(
							'password',
							NULL,
							array('type' => 'password', 'id' => 'login_password', 'class' => 'input-xlarge')
						);?>
					</div>
				</div>
			</div>
			<?php if($can_remember):?>
				<div class="control-group">
					<div class="controls">
						<label class="checkbox">
							<?php echo Form::checkbox(
								'remember',
								1,
								(bool) Arr::get($post, 'remember'),
								array('type' => 'checkbox', 'id' => 'login_remember',)
							);?>
							<?php echo __('Запомнить меня')?>
						</label>
					</div>
				</div>
			<?php endif;?>
			<div class="form-actions">
				<div class="btn-toolbar">
					<div class="btn-group">
						<?php echo Form::button(NULL, __('Войти'), array ('type' => 'submit', 'class' => 'btn btn-primary'));?>
					</div>
				</div>
			</div>
		</fieldset>
		<?php echo Form::close(); ?>
	</div>
</div>