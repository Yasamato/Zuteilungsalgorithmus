// read and load the existing confiuration into the website
window.onload = read_configuration;

// set the html UI values to the config values
function read_configuration() {
    // a is an array generated by php code and has the following structure
    // var a = ["schuelerAnzahl", "checkedMonday", ..., "checkedFriday", "firstDay"]
    document.getElementById("inputSchuelerAnzahl").value = parseInt(a[0]);
    for (var i = 1; i <= 5; i++) {
        // convert the string into a bool
        a[i] = (a[i] == 'true');
        document.getElementById("inlineCheckbox"+i).checked = a[i];
    }
    // convert the string into a bool
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
