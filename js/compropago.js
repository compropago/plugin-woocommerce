jQuery(document).ready(function($) {
	$.fancybox.open({
		href : gateway_compropago,
		type : 'iframe',
		padding : 5
	});
	$("#payment_btn").click(function(event) {
		event.preventDefault();
		$.fancybox.open({
			href : gateway_compropago,
			type : 'iframe',
			padding : 5
		});
	});
});