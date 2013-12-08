<?php defined('SYSPATH') or die('No direct access allowed.');?>
<?php if($errors):?>
<div class="alert alert-error">
	<?php foreach($errors as $block => $_errors): foreach($_errors as $error):?>
	<div><?php echo $error?></div>
	<?php endforeach;endforeach;?>
</div>
<?php endif;?>
<?php echo Form::open(Request::current(), array('class' => 'form-horizontal'));?>
	<?php echo Form::hidden('user[id]', Arr::get($post['user'], 'id'))?>

	<div class="control-group">
		<?php echo Form::label('last_name', 'Фамилия', array('class' => 'control-label'))?>
		<?php if (isset($errors['user']['last_name'])): ?>
			<div class="form-error"><?php echo $errors['user']['last_name'] ?></div>
		<?php endif?>
		<div class="controls">
			<?php echo Form::input('user[last_name]', $post['user']['last_name'], array('id' => 'last_name'))?>
		</div>
	</div>
	<div class="control-group">
		<?php echo Form::label('first_name', 'Имя', array('class' => 'control-label'))?>
		<?php if (isset($errors['user']['first_name'])): ?>
			<div class="form-error"><?php echo $errors['user']['first_name'] ?></div>
		<?php endif?>
		<div class="controls">
			<?php echo Form::input('user[first_name]', $post['user']['first_name'], array('id' => 'first_name'))?>
		</div>
	</div>
	<div class="control-group">
		<?php echo Form::label('patronymic', 'Отчество', array('class' => 'control-label'))?>
		<?php if (isset($errors['user']['patronymic'])): ?>
			<div class="form-error"><?php echo $errors['user']['patronymic'] ?></div>
		<?php endif?>
		<div class="controls">
			<?php echo Form::input('user[patronymic]', $post['user']['patronymic'], array('id' => 'patronymic'))?>
		</div>
	</div>

	<div class="control-group">
		<?php echo Form::label('email', 'Email', array('class' => 'control-label'))?>
		<?php if (isset($errors['user']['email'])): ?>
			<div class="form-error"><?php echo $errors['user']['email'] ?></div>
		<?php endif?>
		<div class="controls">
			<?php echo Form::input('user[email]', $post['user']['email'], array('id' => 'email'))?>
		</div>
	</div>
	<div class="control-group">
		<?php echo Form::label('password', __('Пароль'), array('class' => 'control-label'))?>
		<?php if (isset($errors['user']['password'])): ?>
			<div class="form-error"><?php echo $errors['user']['password'] ?></div>
		<?php endif?>
		<div class="controls">
			<?php echo Form::password('user[password]', $post['user']['password'], array('id' => 'password'))?>
		</div>
	</div>
	<div class="control-group">
		<?php echo Form::label('password_confirm', __('Подтверждение пароля'), array('class' => 'control-label'))?>
		<?php if (isset($errors['user']['password_confirm'])): ?>
			<div class="form-error"><?php echo $errors['user']['password_confirm'] ?></div>
		<?php endif?>
		<div class="controls">
			<?php echo Form::password('user[password_confirm]', NULL, array('id' => 'password_confirm'))?>
		</div>
	</div>
	<div class="control-group">
		<?php echo Form::label('roles', __('Роли пользователя'), array('class' => 'control-label'))?>
		<?php if (isset($errors['user']['roles'])): ?>
			<div class="form-error"><?php echo $errors['user']['roles'] ?></div>
		<?php endif?>
		<div class="controls">
			<?php echo Form::select('user[roles][]', $roles, $post['user']['roles'], array('id' => 'roles', 'multiple' => 'multiple'))?>
		</div>
	</div>
	<div class="control-group">
		<?php echo Form::label('is_active', __('Статус активности'), array('class' => 'control-label'))?>
		<?php if (isset($errors['user']['is_active'])): ?>
			<div class="form-error"><?php echo $errors['user']['is_active'] ?></div>
		<?php endif?>
		<div class="controls">
			<?php echo Form::select('user[is_active]', $statuses, $post['user']['is_active'], array('id' => 'is_active'))?>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<?php echo Html::anchor(
				$cancel_link,
				__('Отмена'),
				array('class' => 'btn', 'title' => __('Отмена')));?>
			<?php echo Form::button(
				NULL,
				__('Сохранить'),
				array ('class' => 'btn btn-success'));?>
		</div>
	</div>
<?php  echo Form::close(); ?>