<?php defined('SYSPATH') or die('No direct access allowed.');?>
<?php if($errors):?>
<div class="error">
	<?php foreach($errors as $block => $_errors): foreach($_errors as $error):?>
	<div><?php echo $error?></div>
	<?php endforeach;endforeach;?>
</div>
<?php endif;?>
<div class="edit-content-form">
<?php echo Form::open(Request::current());?>
	<?php echo Form::hidden('user[id]', Arr::get($post['user'], 'id'))?>
	<?php echo Form::hidden('user_data[id]', Arr::get($post['user_data'], 'id'))?>

	<div class="form-item">
		<?php echo Form::label('last_name', 'Фамилия')?>

		<?php if (isset($errors['user_data']['last_name'])): ?>
			<div class="form-error"><?php echo $errors['user_data']['last_name'] ?></div>
		<?php endif?>
		<?php echo Form::input('user_data[last_name]', $post['user_data']['last_name'], array('id' => 'last_name'))?>
	</div>
	<div class="form-item">
		<?php echo Form::label('first_name', 'Имя')?>

		<?php if (isset($errors['user_data']['first_name'])): ?>
			<div class="form-error"><?php echo $errors['user_data']['first_name'] ?></div>
		<?php endif?>
		<?php echo Form::input('user_data[first_name]', $post['user_data']['first_name'], array('id' => 'first_name'))?>
	</div>
	<div class="form-item">
		<?php echo Form::label('patronymic', 'Отчество')?>

		<?php if (isset($errors['user_data']['patronymic'])): ?>
			<div class="form-error"><?php echo $errors['user_data']['patronymic'] ?></div>
		<?php endif?>
		<?php echo Form::input('user_data[patronymic]', $post['user_data']['patronymic'], array('id' => 'patronymic'))?>
	</div>

	<div class="form-item">
		<?php echo Form::label('email', 'Email')?>
		<?php if (isset($errors['user']['email'])): ?>
			<div class="form-error"><?php echo $errors['user']['email'] ?></div>
		<?php endif?>
		<?php echo Form::input('user[email]', $post['user']['email'], array('id' => 'email'))?>
	</div>
	<div class="form-item">
		<?php echo Form::label('password', __('Пароль'))?>
		<?php if (isset($errors['user']['password'])): ?>
			<div class="form-error"><?php echo $errors['user']['password'] ?></div>
		<?php endif?>
		<?php echo Form::password('user[password]', $post['user']['password'], array('id' => 'password'))?>
	</div>
	<div class="form-item">
		<?php echo Form::label('password_confirm', __('Подтверждение пароля'))?>
		<?php if (isset($errors['user']['password_confirm'])): ?>
			<div class="form-error"><?php echo $errors['user']['password_confirm'] ?></div>
		<?php endif?>
		<?php echo Form::password('user[password_confirm]', NULL, array('id' => 'password_confirm'))?>
	</div>
	<div class="form-item">
		<?php echo Form::label('roles', __('Роли пользователя'))?>
		<?php if (isset($errors['user']['roles'])): ?>
			<div class="form-error"><?php echo $errors['user']['roles'] ?></div>
		<?php endif?>
		<?php echo Form::select('user[roles][]', $roles, $post['user']['roles'], array('id' => 'roles', 'multiple' => 'multiple'))?>
	</div>
	<div class="form-item">
		<?php echo Form::label('is_active', __('Роли пользователя'))?>
		<?php if (isset($errors['user']['is_active'])): ?>
			<div class="form-error"><?php echo $errors['user']['is_active'] ?></div>
		<?php endif?>
		<?php echo Form::select('user[is_active]', $statuses, $post['user']['is_active'], array('id' => 'is_active'))?>
	</div>

	<div class="form-item text-right">
		<?php echo Html::anchor(
			Route::url('admin', array(
				'controller' => 'user',
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