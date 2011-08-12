<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
	<title><?php echo $title?> :: <?php echo $company_name?> Controll System</title>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="ru-ru" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<?php foreach ($styles as $file => $type) echo HTML::style($file, array('media' => $type), TRUE), "\n" ?>
	<?php foreach ($scripts as $file) echo HTML::script($file), "\n" ?>
	<!--[if lte IE 6]>
		<?php echo Html::script('media/js/DD_belatedPNG.js'); ?>
		<script type="text/javascript">
			DD_belatedPNG.fix('#menu, #ed-panel, #ed-panel *, img');
		</script>
	<![endif]-->
<script type="text/javascript">var frontend = false;</script>
</head>

<body>
	<div id="main">
		<div id="header">
			<div id="header-wrapper">
				<?php echo html::anchor(
						'admin',
						html::image('admin/media/i/logo.png', array(
							'id' => 'logo',
							'alt' => $company_name
						)))?>
				<?php echo $company_name?>
				<div id="ed-panel" <?php echo ($ed_copy === FALSE) ? 'class="nologo"' : ''?>>
					<ul>
						<li class="profile"><?php echo html::anchor(
							Route::get('admin')
								->uri(array('controller' => 'user', 'action' => 'profile')),
							__('Профиль'))?></li>
						<li class="help"><?php echo html::anchor(
							Route::get('admin')
								->uri(array('controller' => 'help', 'action' => '')),
						__('Помощь'))?></li>
						<li class="exit"><?php echo html::anchor(
							Route::get('admin')
								->uri(array('controller' => 'auth', 'action' => 'logout')),
						__('Выход'))?></li>
					</ul>
				</div>
			</div>
			<?php echo $menu->generate('admin')?>
		</div>
		<div id="content">
			<div id="right-side">
				<!--<div class="list-items-block">
					<h1 class="title">Блок для новостей</h1>
					<div class="item">
						<p>Ракета, как следует из системы уравнений, участвует в погрешности определения курса меньше, чем сублимированный гироскопический прибор, что не влияет при малых значениях коэффициента податливости.</p>
						<p><a href="#" class="orange">Подробнее</a></p>
					</div>
					<div class="item">
						<p>Ракета, как следует из системы уравнений, участвует в погрешности определения курса меньше, чем сублимированный гироскопический прибор, что не влияет при малых значениях коэффициента податливости.</p>
						<p><a href="#" class="orange">Подробнее</a></p>
					</div>
					<div class="item">
						<p>Ракета, как следует из системы уравнений, участвует в погрешности определения курса меньше, чем сублимированный гироскопический прибор, что не влияет при малых значениях коэффициента податливости.</p>
						<p><a href="#" class="orange">Подробнее</a></p>
					</div>
				</div>-->
				<h1>Персональные данные</h1>
				<div class="info-block">
					<div class="info-block-wrapper">
						<p>
							<img src="/admin/media/i/icons/user.png" alt="" class="ico 16x16" /> <span class="big"><?php echo $user->name?></span>
						</p>
						<p>
							<img src="/admin/media/i/icons/acl-admin.png" alt="" class="ico 16x16" /> <span class="acl-role admin-role">Администратор</span>
						</p>
						<!--<p>
							<img src="/admin/media/i/icons/acl-manager.png" alt="" class="ico 16x16" /> <span class="acl-role manager-role">Менеджер</span>
						</p>
						<p>
							<img src="/admin/media/i/icons/acl-content.png" alt="" class="ico 16x16" /> <span class="acl-role content-role">Контентер</span>
						</p>--->
						<p>
							<img src="/admin/media/i/icons/mail.png" alt="" class="ico 16x16" /> <?php echo html::mailto($user->email, $user->email, array('class' => 'orange'))?>
						</p>
					</div>
					<div class="info-block-status">
						<div class="info-block-status-wrapper">
							Последний заход: <strong><?php echo date('d M Y', $user->last_login)?></strong> | <?php echo date('H:i', $user->last_login)?>
						</div>
					</div>
				</div>
				<?php echo $right_content;?>
			</div>

			<div id="left-side">
				<div class="dashed">
					<h1 class="title"><?php echo $page_title?></h1>
				</div>
				<?php echo $content;?>
				<div class="clear-both"></div>
			</div>
			<div class="clear-both"></div>
		</div>
		<div id="main-wrapper"></div>
	</div>
	<div id="footer">
		<?php echo ($ed_copy === TRUE) ? '&copy; Система управления сатйом разработана в студии <a href="http://enerdesign.ru/">EnerDesign</a>' : ''?>
	</div>
<?php echo $debug?>
</body>
</html>
