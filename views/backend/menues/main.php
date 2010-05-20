<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<?php $active = NULL ?>
<ul id="menu">
	<?php foreach($menu as $name => $item): if($item['visible']):?>
		<li class="<?php echo $item['class'] ?>">
			<?php
				echo Html::anchor(
					$item['href'],
					$item['title'],
					array('title' => $item['title'])
				);
			?>
			<?php if ($item['class'] == 'active'): ?>
				<?php $active = $name ?>
			<?php endif ?>
			<?php //$active = ($active === NULL) ? array_pop($menu) : $menu[$active] ?>
			<?php if (count($item['submenu'])): ?>
				<ul>
				<?php foreach($item['submenu'] as $sub_item): if($sub_item['visible']):?>
					<li>
						<?php
							echo Html::anchor(
								$sub_item['href'],
								$sub_item['title'],
								array('title' => $sub_item['title'])
							);
						?>
					</li>
				<?php endif; endforeach ?>
				</ul>
			<?php endif ?>
		</li>
	<?php endif; endforeach ?>
</ul>