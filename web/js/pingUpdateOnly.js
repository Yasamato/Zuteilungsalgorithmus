window.onload = function() {
	var autoUpdate = setInterval(checkUp, 2000);
}

function checkUp() {
	$.get("ping.php", function(data, status) {
		if (status = "success") {
			console.log("Check-Up erhalten, wende Daten-Update an");
			data = JSON.parse(data);
			updateUpdate(data["updateRunning"]);
		}
		else {
			console.log("JS-Data Update failed!!");
		}
	});
}
