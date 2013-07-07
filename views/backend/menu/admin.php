<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<div class="nav-collapse collapse">
	<ul class="nav">
		<?php foreach($menu_arr as $name => $item): if(Arr::get($item, 'is_visible', FALSE)): $has_childs = count($item['childrens']);?>
			<li class="<?php echo $item['active_class']; echo ($has_childs) ? ' dropdown' : NULL; ?>">
				<?php
				$title = ($has_childs) ? $item['title']. ' <b class="caret"></b>' : $item['title'];
				echo HTML::anchor(
					Arr::get($item, 'href'),
					$title,
					array(
						'title'       => $item['title'],
						'class'       => ($has_childs) ? 'dropdown-toggle' : NULL,
						'data-toggle' => ($has_childs) ? 'dropdown' : NULL,
					)
				);
				?>
				<?php if (count($item['childrens'])): ?>
					<ul class="dropdown-menu">
						<?php foreach($item['childrens'] as $sub_item): if($sub_item['is_visible']):?>
							<li>
								<?php
								echo HTML::anchor(
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
</div>