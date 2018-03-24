window.onload = read_configuration;

function read_configuration() {
    //var a = ["schuelerAnzahl", "checkedMonday", ..., "checkedFriday", "firstDay"]
    document.getElementById("inputSchuelerAnzahl").value = parseInt(a[0]);
    for (var i = 1; i <= 5; i++) {
        a[i] = (a[i] == 'true');
        document.getElementById("inlineCheckbox"+i).checked = a[i];
    }
    a[6] = (a[6] == 'true');
    document.getElementById("firstDaytrue").checked = a[6];
}

//We don't know why we need it, but now it works!!! Don't delete it !!!!!!!
$('input:checkbox').on('click', function(e) {
    e.stopImmediatePropagation();
    var checked = (e.currentTarget.checked) ? false : true;
    e.currentTarget.checked = (checked) ? false : checked.toString();
});

$('input:radio').on('click', function(e) {
    e.stopImmediatePropagation();
    var checked = (e.currentTarget.checked) ? false : true;
    e.currentTarget.checked = (checked) ? false : checked.toString();
});
