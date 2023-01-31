// Виджет онлайн записи, Автоград
$("<div/>", {id: '__aor_allw_block', text: ''}).appendTo("body");
$("#__aor_allw_block").load( "http://booking.agrad.ru/aor-load.php", {"selectize": "yes", "jsmask": "yes"}, function(){/*cb*/});