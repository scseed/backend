<!DOCTYPE html>
<html lang="ru">
<head>
	<title><?php echo $title?> :: <?php echo $company_name?> Controll System</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="ru-ru" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<?php echo StaticCss::instance()->get_all()?>
<script type="text/javascript">var frontend = false;</script>
</head>

<body>
	<?php if($logged_in):?>
	<!-- Part 1: Wrap all page content here -->
	<div id="wrap">
		<!-- Fixed navbar -->
		<div class="navbar navbar-fixed-top navbar-inverse">
			<div class="navbar-inner">
				<div class="container">
					<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<?php echo HTML::anchor('admin', $company_name, array('class' => 'brand'))?>
					<?php echo $menu->generate('admin')?>
					<!--/.nav-collapse -->
				</div>
			</div>
		</div>
		<?php endif;?>

		<!-- Begin page content -->
		<div class="container">
			<?php if($logged_in):?>
			<div class="page-header">
				<h1 class="title"><?php echo $page_title?></h1>
			</div>
			<?php endif;?>
			<?php echo $content;?>
		</div>

		<div id="push"></div>
	</div>
	<?php if($logged_in):?>
	<div id="footer">
		<div class="container">
			<p class="muted credit"><?php echo $company_name?></p>
		</div>
	</div>
	<?php endif;?>
	<?php echo StaticJs::instance()->get_all()?>
</body>
</html>
