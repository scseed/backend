<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<h2>Добро пожаловать в административную зону сайта компании <?php echo $company_name?>!</h2>
<p>Для перехода в интересующий Вас раздел, воспользуйтесь навигационным меню.</p>
<p>Если Вы в первый раз зашли, то воспользуйтесь <?php echo html::anchor('admin/help', 'разделом помощи')?>, чтобы узнать про основы работы с системой.</p>
<p>При возникновении трудностей, которые не описаны в разделе помощи или при появлении ошибок, воспользуйтесь <?php echo html::anchor('admin/feedback', 'формой связи с администратором ресурса')?>.</p>
<div class="dashed">
<!--<h1>Последние изменения в системе</h1>-->
<!--</div>-->
<!--<table>-->
<!--	<tr>-->
<?php //foreach($logs_meta->fields() as $meta):?>
<!--		<th>--><?php //echo $meta->label?><!--</th>-->
<?php //endforeach;?>
<!--	</tr>-->
<!---->
<?php //foreach ($logs as $log):?>
<!--	<tr>-->
<!--		<td>--><?php //echo $log->id?><!--</td>-->
<!--		<td>--><?php //echo date($logs_meta->fields('time')->pretty_format, $log->time)?><!--</td>-->
<!--		<td>--><?php //echo __($log->type->name)?><!--</td>-->
<!--		<td>--><?php //echo __($log->result->name)?><!--</td>-->
<!--		<td>--><?php //echo ($log->user->name) ? $log->user->name : __('неизвестен')?><!--</td>-->
<!--		<td>--><?php //echo $log->description?><!--</td>-->
<!--	</tr>-->
<?php //endforeach;?>
<!---->
<!---->
<!--</table>-->