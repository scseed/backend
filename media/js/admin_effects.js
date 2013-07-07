$(document).ready(function(){

	$('[rel="tooltip"]').tooltip({});

	$('.btn-loading')
	.click(function () {
		var btn = $(this);
		btn.button('loading');
	});

	var datepicker = $('.datepicker');
	if(datepicker.length)
	{
		datepicker.datepicker({
			format: "dd.mm.yyyy",
			weekStart: 1,
			todayBtn: "linked",
			language: "ru",
//			calendarWeeks: true,
			autoclose: true,
			todayHighlight: true
		});
	}

	$('#add_answer').click(function(){
		$('#poll_answers').append('<div class="form-item"><label for="answers[]">Вариант ответа <span class="delete_answer">удалить</span></label><input type="text" name="answers[]"></div>');
	});

	$('.delete_answer').click(function(){
		$(this).closest('.form-item').remove();
	});

	$('.filter_item').each(function(){
		$(this).css({
			width:$(this).find('ul').width() + 22
		});
	});

	$('.filter_item ul').hover(
		function(){
			$(this).addClass('open').show(200);
		},
		function () {
			$(this).hide(200, function() {$(this).show(); $(this).removeClass('open')});
		}
	);
//
//	if($('textarea').length > 0)
//		$('textarea').ckeditor();

	$('.page_content legend').click(function(){
		toggle_fieldset_block(this);
	});

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

$(function() {
	var dateTo   = $( "#date_to" )
	  , dateFrom = $( "#date_from" )
	;
	dateFrom.datepicker().on('changeDate', function(ev){
		dateTo.datepicker('setStartDate', new Date(ev.date));
		dateTo.datepicker('show');
	});

	dateTo.datepicker().on('changeDate', function(ev){
		dateFrom.datepicker('setEndDate', new Date(ev.date));
	});
});