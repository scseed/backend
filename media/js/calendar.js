$(document).ready(function(){
	if($('input[class=datetime]').length)
	{
		$('input[class=datetime]').datetime();
	}
	if($('input[class=time]').length)
	{
		$('input[class=time]').datetime({ withDate: false, format: 'hh:ii' });
	}
});