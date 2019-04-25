window.onload = function() {
	var autoUpdate = setInterval(function () {
		$.get("adminPing.php", function(data, status) {
			if (status = "success") {
				console.log("Update erhalten");
				data = JSON.parse(data);
				updateProgressbar(data["algorithmusRunning"]);
				window.projekte = data["projekte"];
				window.wahlen = data["wahlen"];
				window.zwangszuteilungen = data["zwangszuteilungen"];
				window.klassen = data["klassen"];
				window.klassenliste = data["klassenliste"];
			}
			else {
				console.log("JS-Data Update failed!!");
			}
		});
	}, 3000);
}

function updateProgressbar(data) {
	if (data == "false") {
		console.log("Algorithmus läuft nicht");
		return;
	}
	data = parseFloat(data) * 100;
	console.log("Fortschritt: " + data + "%");
	if (data != 100) {
		$($('.progress-bar')[0]).css('width', data + '%').html((data > 50 ? (Math.round(data * 100) / 100) + "%" : ""));
		$($('.progress-bar')[1]).css('width', (100 - data) + '%').html((data < 50 ? (Math.round(data * 100) / 100) + "%" : ""));
	} else {
		$($('.progress-bar')[0]).css('width', '100%').html("100%");
		$($('.progress-bar')[1]).css('width', '0%').html("");
		setTimeout(function () {
			window.location.href = "?";
		}, 2000);
	}
}
