<?php defined('SYSPATH') or die('No direct access allowed.');?>
<div class="edit-content-form">
<?php echo Form::open();?>
<?php foreach ($item->meta()->fields() as $field): if($field->in_form): $value = $item->{$field->name};?>
	<div class="form-item">
		<?php echo Form::label('filter_' . $field->name, $field->label)?>

		<?php if (isset($errors[$field->name])): ?>
			<div class="form-error"><?php echo $errors[$field->name] ?></div>
		<?php endif?>

<?php

	switch($field)
	{
		case $field instanceof Jelly_Field_BelongsTo:
	        $collection_name = inflector::plural($field->name);
			echo Form::select($field->name, $$collection_name, $value->id, array('id' => 'filter_'.$field->name));
	        break;
		case $field instanceof Jelly_Field_ManyToMany:
			echo Form::select($field->name, ${$field->name}, $value->as_array('id'), array('id' => 'filter_'.$field->name));
	        break;
	    case $field instanceof Jelly_Field_Text:
	        echo Form::textarea($field->name, $value);
	        break;
		case $field instanceof Jelly_Field_Password:
	        echo Form::password($field->name, $value);
	        break;
		case $field instanceof Jelly_Field_Integer:
		case $field instanceof Jelly_Field_String:
	        echo Form::input($field->name, $value);
	         break;
		case $field instanceof Jelly_Field_Boolean:
	        $values = array(
		        TRUE => $field->label_true,
		        FALSE => $field->label_false,
	        );
	        $value = ($value === NULL OR $value == '') ? $field->default : $value;
	        echo Form::select($field->name, $values, $value, array('id' => 'filter_'.$field->name));
	        break;
	}

?>
	</div>
<?php endif; endforeach;?>
	<div class="form-item text-right">
		<?php echo Html::anchor(
			Route::get('admin')
				->uri(array(
						'controller' => $controller,
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