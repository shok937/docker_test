// js
$(function () {
  // ya m goal
  if (window.ym)
  {
    ym(57064057,'reachGoal','fb-w-open');
  }
});

// parse GET
function parse_query_string(query) {
  var vars = query.split("&");
  var query_string = {};
  for (var i = 0; i < vars.length; i++) {
    var pair = vars[i].split("=");
    var key = decodeURIComponent(pair[0]);
    var value = decodeURIComponent(pair[1]);
    // If first entry with this name
    if (typeof query_string[key] === "undefined") {
      query_string[key] = decodeURIComponent(value);
      // If second entry with this name
    } else if (typeof query_string[key] === "string") {
      var arr = [query_string[key], decodeURIComponent(value)];
      query_string[key] = arr;
      // If third or later entry with this name
    } else {
      query_string[key].push(decodeURIComponent(value));
    }
  }
  return query_string;
}

var query = window.location.search.substring(1);
var qs = parse_query_string(query);
//console.log(qs);
// set global
if (qs['fbsource']) {window.fbsource = qs['fbsource']}


$(function () {
	aorFBAlert($('.__alkfb-feedbackform-block').html(), 'minhtml');
	$('.__alkfb-window-inner .__alkfb_p_inp').mask("+7 (999) 999-99-99");
});

$(document).on("click", ".__aorfb_c_b_i_sendwinfo", function(e){
	let alkfbText = $('.__alkfb-window-inner .__alkfb-feedbackform-problemtxt-ta').val();
	let alkfbPhone = $('.__alkfb-window-inner .__alkfb_p_inp').val();
	let alkfbEmail = $('.__alkfb-window-inner .__alkfb_e_inp').val();

	if (!alkfbText || !alkfbPhone || alkfbText.length<1 || alkfbPhone.length<1)
	{
		$('.__alkfb-window-inner .__alkfb-feedbackform-problemtxt-ta, .__alkfb-window-inner .__alkfb_p_inp').addClass('redstyle');
		if (alkfbText.length>0)
		{
			$('.__alkfb-window-inner .__alkfb-feedbackform-problemtxt-ta').removeClass('redstyle');
		}
		if (alkfbPhone.length>0)
		{
			$('.__alkfb-window-inner .__alkfb_p_inp').removeClass('redstyle');
		}
		alert('Необходимо заполнить поля описания ошибки и телефона.');
		return false;
	}
  else if (alkfbPhone.indexOf('+7 (8') != -1 || alkfbPhone.indexOf('+7 (7') != -1)
  {
    $('.__alkfb-window-inner .__alkfb_p_inp').addClass('redstyle');
    alert('Указанный Вами номер '+ alkfbPhone + ' скорее всего с ошибкой. Перепроверьте его, пожалуйста.');
    return false;
  }
	else
	{
		$('.__alkfb-window-inner .__alkfb-feedbackform-problemtxt-ta, .__alkfb-window-inner .__alkfb_p_inp').removeClass('redstyle');
	}

	let thisFBSource = 'unknow';
	if (fbsource && fbsource.length>0)
	{
		thisFBSource = fbsource;
	}

	// send 
	weel();
	$.post( '/feedback_r.php', { 'fbph': alkfbPhone, 'fbem': alkfbEmail, 'fbte': alkfbText, 'fromsource': thisFBSource })
	    .done(function( fbdata ) 
	{
	          
	    if (fbdata && fbdata.length > 0 && alkfb_isJSON(fbdata)) { fbdata = JSON.parse(fbdata); }
	    //send upd recorded
	    if (fbdata && fbdata.ok && fbdata.ok == true)
	    {
	      $('.__alkfb-feedbackform_vform').hide();
	      $('.__alkfb-feedbackform_successnote').html('Сообщение об ошибке <b class="greentext">успешно</b> отправлено. Мы обязательно рассмотрим проблему в ближайшее время.').show();
	      unweel();

        // ya m goal
        if (window.ym)
        {
          ym(57064057,'reachGoal','fb-w-sendmessage');
        }

	    }
	    else
	    {
	      alert('Не получилось отправить сообщение об ошибке (b). Попробуйте ещё раз чуть позже.');
	      console.warn("feedback_r(b) state error");
	      console.log(fbdata);
	      unweel();
	    }
	}).fail(function() {
	    alert('Не получилось отправить сообщение об ошибке. Попробуйте ещё раз чуть позже.');
	    console.warn("feedback_r state error");
	    unweel();
	});


});

function aorFBAlert(alertMsg, alertMode='')
{
  // it's message
  $(".__alkfb-window-i-message").show();
  $(".__alkfb-window-close").show();
  // add
  $(".__alkfb-window-i-message").text(""); // clear
  if (alertMsg && alertMsg.length > 0) 
  { 
    if (alertMode == 'html')
    {
      $(".__alkfb-window-block").addClass("alkwbflex");
      $(".__alkfb-window-i-message").html(alertMsg); 
    }
    else
    {
      $(".__alkfb-window-block").removeClass("alkwbflex");
      if (alertMode == 'minhtml')
      {
        $(".__alkfb-window-i-message").html(alertMsg); 
      }
      else
      {
        $(".__alkfb-window-i-message").text(alertMsg); 
      }
    }
  }
  
  // show
  $(".__alkfb-window-overlay").fadeIn("fast", function () {
    $(".__alkfb-window-inner").fadeIn("fast");
  });
}
// custom d.t. close action
$(document).on("click", ".__alkfb-window-overlay, .__alkfb-window-close", function(e){
  // animate close
  $(".__alkfb-window-inner").fadeOut("fast", function () {
    $(".__alkfb-window-overlay").fadeOut("fast");

    // send close frame to parent window
    var targetWindow = window.parent;
    targetWindow.postMessage("docloseFBiframe", "*");
  });
});


function alkfb_isJSON(something) {
    if (typeof something != 'string')
        something = JSON.stringify(something);

    try {
        JSON.parse(something);
        return true;
    } catch (e) {
        return false;
    }
}


function weel()
{
  $('.__alkfb-weel').show();
}
function unweel()
{
  $('.__alkfb-weel').hide();
}