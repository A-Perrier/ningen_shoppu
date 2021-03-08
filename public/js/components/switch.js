let checkbox = $("input[type=checkbox]");

$("input[type=checkbox].onSale").wrap('<label class="switch"></label>');
$("label.switch").append('<span class="slider round"></span>');