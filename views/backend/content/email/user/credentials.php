<?php defined('SYSPATH') or die('No direct access allowed.');?>
<h3>Здравствуйте, <?php echo $user->first_name?></h3>

<p>Вам была создана учётная запись на сайте <strong><?php echo $site->site_name?></strong>.
Для входа в админ-центр воспользуйтесь следующими данными:</p>

<p>
	<strong>Страница авторизации:</strong> <?php echo HTML::anchor(Route::url('admin', array('controller' => '', 'action' => ''), 'http'))?><br/>
	<strong>Логин (email):</strong> <?php echo $user->email?><br/>
	<strong>Пароль:</strong> <?php echo $password?><br/>
</p>