<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<ul id="menu">
	<?php foreach($menu as $item): ?>
		<li class="<?php echo $item['class'] ?>">
			<?php
				echo Html::anchor(
					$item['href'],
					$item['title'],
					array('title' => $item['title'])
				);
			?>
			<?php if (count($item['submenu'])): ?>
				<ul>
				<?php foreach($item['submenu'] as $sub_item): ?>
					<li>
						<?php
							echo Html::anchor(
								$sub_item['href'],
								$sub_item['title'],
								array('title' => $sub_item['title'])
							);
						?>
					</li>
				<?php endforeach ?>
				</ul>
			<?php endif ?>
		</li>
	<?php endforeach ?>
</ul>