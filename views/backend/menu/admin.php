<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<ul id="menu">
	<?php foreach($menu_arr as $name => $item): if($item['is_visible']):?>
		<li class="<?php echo $item['active_class'] ?>">
			<?php
				echo Html::anchor(
					$item['href'],
					$item['title'],
					array('title' => $item['title'])
				);
			?>
			<?php if (count($item['childrens'])): ?>
				<ul>
				<?php foreach($item['childrens'] as $sub_item): if($sub_item['is_visible']):?>
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