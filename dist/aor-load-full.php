<?php
// Agrad Online Record Widget, Полная HTML страница (для iframe)
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Онлайн запись, Автоград</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1">
</head>
<body>

<!-- <script type="text/javascript" src="libs/jquery-3.5.1.min.js"></script> -->
<script type="text/javascript" src="libs/jquery.min.js"></script>
<script type="text/javascript" src="agradfeedback-widget-iframe.js?1"></script>
<script type="text/javascript">window.thisfbsource = 'booking.agrad.ru';</script>

<?php 
if (isset($_GET['aorfltr_operations']) && !empty($_GET['aorfltr_operations'])):
?>
  <script type="text/javascript">window.thisfltr_operations = decodeURIComponent('<?php echo htmlentities($_GET['aorfltr_operations'], ENT_QUOTES); ?>');</script>
<?php
endif;
?>

<!-- AOR -->

<?php

if (isset($_POST['basket_composition'], $_POST['client_info'])):
  // корзина из личного кабинета
  // JSON.parse($('.__aor_frombasket_basketcomposition').text())
  // JSON.parse($('.__aor_frombasket_clientinfo').text())
  ?>
    <div class="__aor_frombasket_basketcomposition" style="display:none;"><?php echo $_POST['basket_composition']; ?></div>
    <div class="__aor_frombasket_clientinfo" style="display:none;"><?php echo $_POST['client_info']; ?></div>
  <?php
endif;

?>


<?php

if (isset($_POST['rerecord_data'], $_POST['client_info'])):
  // перезапись из личного кабинета
  // JSON.parse($('.__aor_frombasket_basketcomposition').text())
  // JSON.parse($('.__aor_frombasket_clientinfo').text())
  ?>
    <div class="__aor_fromlk_rerecordinfo" style="display:none;"><?php echo $_POST['rerecord_data']; ?></div>
    <div class="__aor_fromlk_clientinfo" style="display:none;"><?php echo $_POST['client_info']; ?></div>
  <?php
endif;

?>


<script>
  $("<div/>", {id: '__aor_allw_block', text: ''}).appendTo("body");
  $("#__aor_allw_block").load( "/aor-load.php", {"selectize": "yes", "jsmask": "yes", "autoclose": "yes", "to": "<?php 
  
  // переход по уникальной ссылке
  if (isset($_GET['to']) && preg_match('/^[A-Za-z0-9-]+$/', $_GET['to']))
  {
    echo $_GET['to'];
  }
  
  ?>"}, function(){  openAOR(); <?php if (isset($_GET['to']) && strlen($_GET['to']) > 0):?> setTimeout(aorTOstart, 400); <?php endif;?>  });
</script>

<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(53948962, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/53948962" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

</body>
</html>