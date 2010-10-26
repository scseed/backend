<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
	<title><?php echo $title?> :: <?php echo $company_name?> Controll System</title>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="ru-ru" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<?php foreach($styles as $file => $type) echo HTML::style($file, array('media' => $type), TRUE), "\n" ?>
	<?php foreach($scripts as $file) echo HTML::script($file), "\n" ?>

	<!--[if lte IE 6]>
		<?php echo Html::script('js/DD_belatedPNG.js'); ?>
		<script type="text/javascript">
			DD_belatedPNG.fix('#menu, #ed-panel, #ed-panel *, img');
		</script>
	<![endif]-->

</head>

<body>
	<div id="main">
		<div id="content">
			<div id="left-side">
				<?php echo $content ?>
			</div>
			<div class="clear-both"></div>
		</div>
		<div id="main-wrapper"></div>
	</div>
	<div id="footer">
		&copy; Система управления сайтом  разработана в студии <a href="http://enerdesign.ru/">EnerDesign</a>
	</div>
	<?php echo $debug?>
</body>
</html>