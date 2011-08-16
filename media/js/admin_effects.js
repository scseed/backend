$(document).ready(function(){
	$('#menu > li > a').each(function(){
		if ($(this).parent('li').find('ul').size() == 0){
			$(this).parent('li').append('<div class="menu-wrapper-line" />');
		}
		else {
			$(this).click(function(){
				$('#menu > li.active').removeClass('active');
				$(this).parent('li').addClass('active');
				return false;
			});
		}
	});

	$('#ed-panel').mouseenter(function(){
		$(this).animate({width: 245}, 200);
	});

	$('#ed-panel').mouseleave(function(){
		$(this).animate({width: 165}, 200);
	});

	if($('input[type=datepicker]').length)
		$('input[type=datepicker]').datepicker({dateFormat: 'dd.mm.yy'});

	$('label[for=features], label[for=qualities], label[for=modes]').each(function(){
		$('#'+$(this).attr('for')).slideUp(200);
		$(this).css('border-bottom', 'dashed 1px #ccc').css('cursor', 'pointer');
	});

	$('label[for=features], label[for=qualities], label[for=modes]').toggle(
		function(){
			$('#'+$(this).attr('for')).slideDown(200);
			$(this).css('border-bottom', 'none');
		},

		function(){
			$('#'+$(this).attr('for')).slideUp(200);
			$(this).css('border-bottom', 'dashed 1px #ccc');
		}
	);

	$('#add_answer').click(function(){
		$('#poll_answers').append('<div class="form-item"><label for="answers[]">Вариант ответа <span class="delete_answer">удалить</span></label><input type="text" name="answers[]"></div>');
	});

	$('.delete_answer').click(function(){
		$(this).closest('.form-item').remove();
	});

	$('.filter_item ul').hover(
		function(){
			$(this).addClass('open').show(200);
		},
		function () {
			$(this).slideUp(200, function() {$(this).show(); $(this).removeClass('open')});
		}
	)

//	$("textarea").markItUp(mySettings);
	$('textarea').ckeditor();

	$('.page_content legend').click(function(){
		toggle_fieldset_block(this);
	})

	$('.delete_content').click(function(){
		var button = this;
		var confirmation = confirm('Вы уверены?');

		if(confirmation)
		{
			$.ajax({
				url: $(this).attr('href'),
				success: function(data){
					$(button).parent().find('input').val('');
					$(button).parent().find('textarea').html('');
					toggle_fieldset_block(button);
					$(button).parent().find('a').remove();
					alert('Содержание удалено');
				}
			});
		}

		return false;
	})
});

function toggle_fieldset_block(object)
{
	if($(object).parent().hasClass('closed'))
	{
		$(object).parent().children('div, a').slideDown();
		$(object).parent().removeClass('closed');
	}
	else
	{
		$(object).parent().children('div, a').slideUp();
		$(object).parent().addClass('closed');
	}
}