$('.modal').modal({
	show: true,
	keyboard: false,
	backdrop: 'static'
});
$("#okbtn").on("click", function() {
	window.location.href = "/";
});
