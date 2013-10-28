<?php defined('SYSPATH') or die('No direct access allowed.');?>
<?php echo Form::open(Request::current(), array('class' => 'form-horizontal'));?>
	<div class="control-group <?php echo (isset($errors['pubdate'])) ? 'error' : NULL?>">
		<?php echo Form::label('pubdate', 'Дата публикации', array('class' => 'control-label'))?>
		<div class="controls">
			<?php echo Form::input('pubdate', $post['pubdate'], array('id' => 'pubdate', 'class' => 'datepicker'))?>
			<?php if (isset($errors['pubdate'])):?>
				<span class="help-inline"><?php echo $errors['pubdate'] ?></span>
			<?php endif?>
		</div>
	</div>
	<div class="control-group <?php echo (isset($errors['title'])) ? 'error' : NULL?>">
		<?php echo Form::label('title', 'Краткий заголовок', array('class' => 'control-label'))?>
		<div class="controls">
			<?php echo Form::input('title', $post['title'], array('id' => 'title', 'class' => 'span5'))?>
			<?php if (isset($errors['title'])):?>
				<span class="help-inline"><?php echo $errors['title'] ?></span>
			<?php endif?>
		</div>
	</div>
	<div class="control-group <?php echo (isset($errors['longtitle'])) ? 'error' : NULL?>">
		<?php echo Form::label('longtitle', 'Полный заголовок', array('class' => 'control-label'))?>
		<div class="controls">
			<?php echo Form::input('longtitle', $post['longtitle'], array('id' => 'longtitle', 'class' => 'span5'))?>
			<?php if (isset($errors['longtitle'])):?>
				<span class="help-inline"><?php echo $errors['longtitle'] ?></span>
			<?php endif?>
		</div>
	</div>
	<div class="control-group <?php echo (isset($errors['introtext'])) ? 'error' : NULL?>">
		<?php echo Form::label('introtext', 'Краткое описание', array('class' => 'control-label'))?>
		<div class="controls">
			<?php if (isset($errors['introtext'])):?>
				<span class="help-inline"><?php echo $errors['introtext'] ?></span>
			<?php endif?>
			<?php echo Form::textarea('introtext', $post['introtext'], array('id' => 'introtext'))?>
		</div>
	</div>
	<div class="control-group <?php echo (isset($errors['text'])) ? 'error' : NULL?>">
		<?php echo Form::label('text', 'Полное описание', array('class' => 'control-label'))?>
		<div class="controls">
			<?php if (isset($errors['text'])):?>
				<span class="help-inline"><?php echo $errors['text'] ?></span>
			<?php endif?>
			<?php echo Form::textarea('text', $post['text'], array('id' => 'text'))?>
		</div>
	</div>
	<div class="control-group">
		<?php echo Form::label('is_active', 'Новость активна?', array('class' => 'control-label'))?>
		<div class="controls">
			<?php echo Form::select('is_active', array(0 => 'Нет', 1 => 'Да'), (bool) $post['is_active'], array('id' => 'is_active'))?>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<?php echo Html::anchor(
				Route::get('admin')
				->uri(array(
					'controller' => 'news',
					'action' => 'list',
				)),
				__('Отмена'),
				array('class' => 'btn', 'title' => __('Отмена'))
			);?>
			<?php echo Form::button(
				'save',
				__('Сохранить'),
				array ('type' => 'submit', 'class' => 'btn btn-success')
			);?>
		</div>
	</div>
<?php  echo Form::close(); ?>
<?php
// CKEditor
include_once DOCROOT.'js/ckfinder/ckfinder.php';
include_once DOCROOT.'js/ckeditor/ckeditor.php';
$CKEditor = new CKEditor();
$ckfinder = new CKFinder();
$CKEditor->basePath = '/js/ckeditor/';
$ckfinder->BasePath = '/js/ckfinder/';
$ckfinder->SetupCKEditorObject($CKEditor);
$CKEditor->config = array(
	'toolbar' => array(
		array( 'Bold','Italic','Underline','Strike','Subscript','Superscript','Blockquote','-','NumberedList','BulletedList','-',

		'Outdent','Indent','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','RemoveFormat','Source',  ),
		array( 'Format','Font','FontSize','TextColor','BGColor' ),
		array( 'HorizontalRule','Image','Link', 'Unlink', 'Anchor' ),
	),
	'width' => '100%',
//	'filebrowserImageBrowseUrl' => '/js/ckfinder/ckfinder.html?Type=Images',
);
$CKEditor->replaceAll();
?>
