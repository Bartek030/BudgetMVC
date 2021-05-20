$(document).ready(function() {
	setTime();
	setDate();

	$('.chatButton').click(function() {
		$("#messageBox").toggle();
	});
	
	$('.dropdown').on('show.bs.dropdown', function() {
		$(this).find('.dropdown-menu').first().stop(true, true).slideDown();
	});
	
	$('.dropdown').on('hide.bs.dropdown', function() {
		$(this).find('.dropdown-menu').first().stop(true, true).slideUp();
	});

	if(isSnackbar == true) {
		if(snachbarType == 'success') {
			showSnackbar(snackbarText, snackbarBGColors.success);
		} else if (snachbarType == 'info') {
			showSnackbar(snackbarText, snackbarBGColors.info);
		} else if (snachbarType == 'fault') {
			showSnackbar(snackbarText, snackbarBGColors.fault);
		}
	}
});

let setTime = function() {
	let currentDate = new Date();
	
	let hour = currentDate.getHours();
	if (hour<10) hour = "0"+hour;
	
	let minute = currentDate.getMinutes();
	if (minute<10) minute = "0"+minute;
	
	let second = currentDate.getSeconds();
	if (second<10) second = "0"+second;

	let year = currentDate.getFullYear();

	let month = currentDate.getMonth() + 1;
	if (month<10) month = "0"+month;

	let day = currentDate.getDate();
	if (day<10) day = "0"+day;

	$('#date').html(day + "-" + month + "-" + year);
	$('#time').html(hour + ":" + minute + ":" + second);
		
	setTimeout("setTime()",1000);
}

let setDate = function() {
	let currentDate = new Date();
	let year = currentDate.getFullYear();

	let month = currentDate.getMonth() + 1;
	if (month < 10) month = "0" + month;

	let day = currentDate.getDate();
	if (day < 10) day = "0" + day;
	$('#operationDate').val(year + '-' + month + '-' + day);
}

let snackbarBGColors = {
	'success' : '#33cc33',
	'info' : '#1a8cff',
	'fault': '#cc2900'
};

let showSnackbar = function (snackbarText, snackbarColor) {

	$("#snackbar").html(snackbarText);
	$("#snackbar").css('background-color', snackbarColor);
	$("#snackbar").addClass("show");

	setTimeout(function() { 
		$("#snackbar").removeClass("show");
	}, 3000);
  }