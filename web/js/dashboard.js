window.onload = function() {
	var autoUpdate = setInterval(function () {
		$.get("adminPing.php", function(data, status) {
			if (status = "success") {
				console.log("Update erhalten");
				data = JSON.parse(data);
				updateProgressbar(data["algorithmusRunning"]);
				updateUpdate(data["updateRunning"]);
				if (window.projekte != data["projekte"]) {
					window.projekte != data["projekte"];
				}
				if (window.wahlen != data["wahlen"]) {
					window.wahlen != data["wahlen"];
				}
				if (window.zwangszuteilungen != data["zwangszuteilungen"]) {
					window.zwangszuteilungen != data["zwangszuteilungen"];
				}
				if (window.klassen != data["klassen"]) {
					window.klassen != data["klassen"];
				}
				if (window.klassenliste != data["klassenliste"]) {
					window.klassenliste != data["klassenliste"];
				}
			}
			else {
				console.log("JS-Data Update failed!!");
			}
		});
	}, 3000);
}

function updateUpdate(data) {
	if (data == "false") {
		console.log("Aktuell kein Update am laufen");
		if (!$("#updateAlert").hasClass("d-none")) {
			$("#updateAlert").addClass("d-none");
		}
		return;
	}
	if ($("#updateAlert").hasClass("d-none")) {
		$("#updateAlert").removeClass("d-none");
	}
	console.log("Update wird durchgeführt");
}

function updateProgressbar(data) {
	if (data == "false") {
		console.log("Algorithmus läuft nicht");
		return;
	}
	console.log("Algorithmus läuft");
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
