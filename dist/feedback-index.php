<?php

// AGRAD feedback

?><!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1">
	<title>Обратная связь, Автоград</title>
	
	<link rel="stylesheet" href="/libs/normalize.css?1">
	<link rel="stylesheet" href="/libs/fonts/fonts-stylesheet.css?1">

	<script type="text/javascript" src="/libs/jquery.min.js"></script>
	<script type="text/javascript" src="/libs/jquery.mask.min.js"></script>

	<script type="text/javascript" src="/libs/agrad-feedback.js?<?php echo time();?>"></script>
	<link rel="stylesheet" href="/libs/agrad-feedback.css?<?php echo time();?>">

  <!-- Yandex.Metrika counter -->
  <script type="text/javascript" >
     (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
     m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
     (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

     ym(57064057, "init", {
          clickmap:true,
          trackLinks:true,
          accurateTrackBounce:true,
          webvisor:true
     });
  </script>
  <noscript><div><img src="https://mc.yandex.ru/watch/57064057" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
  <!-- /Yandex.Metrika counter -->

</head>
<body>

	<div class="__alkfb-feedbackform-block" style="display: none;">
		<div class="__alkfb-feedbackform">
			<div class="__alkfb-feedbackformrow __alkfb-feedbackform-title">Сообщить об ошибке</div>
			
			<div class="__alkfb-feedbackform_successnote" style="display:none;"></div>

			<div class="__alkfb-feedbackform_vform">
				<!-- <div class="__alkfb-feedbackformrow __alkfb-feedbackform-fbpage">Страница ошибки: ???</div> -->
				<div class="__alkfb-feedbackformrow __alkfb-feedbackform-problemtxt">
					Опишите ошибку: <b class="redtext">*</b>
					<textarea class="__alkfb-feedbackform-problemtxt-ta"></textarea>
				</div>
				<div class="__alkfb-feedbackformrow __alkfb-feedbackform-phone"><span class="__alkfb-feedbackform_frowel">Телефон: <b class="redtext">*</b></span>&nbsp;&nbsp;&nbsp;<input type="text" class="__alkfb_p_inp" /></div>
				<div class="__alkfb-feedbackformrow __alkfb-feedbackform-email"><span class="__alkfb-feedbackform_frowel">E-mail:</span>&nbsp;&nbsp;&nbsp;<input type="text" class="__alkfb_e_inp" /></div>
				<div class="__alkfb-feedbackformrow __alkfb-feedbackform-buttonsblock">
					<span class="__aorfb_c_b_i c_b_green __aorfb_c_b_i_sendwinfo">Отправить</span>
				</div>
			</div>

		</div>
	</div>

	<div class="__alkfb-window-block">
	    <div class="__alkfb-window-overlay" style="display: none;"></div>
	    <div class="__alkfb-window-inner" style="display: none;">
	        <div class="__alkfb-window-close">X</div>
	        <div class="__alkfb-window-i-message" style="display: none;"></div>
	    </div>
	</div>
	
	<div class="__alkfb-weel" style="display: none;">
	    <div class="__alkfb-weel-overlay"></div>
	    <div class="__alkfb-weel-inner">
	        <div class="__alkfb-weel-i-c"><img src="/img/boxloader.gif" alt="Загрузка..."></div>
	    </div>
	</div>

</body>
</html>