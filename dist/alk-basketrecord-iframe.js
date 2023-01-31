// Виджет онлайн записи (from alk basket,iframe), Автоград
function openAORfromBasket()
{
  let protocolVersion = 'http';
  if (location.protocol == 'https:') { protocolVersion = 'https'; }
  
  $('#iframeBasketAORallwBlock').remove();
  $('<iframe>', {src: protocolVersion+'://booking.agrad.ru/aor-load-full.php?to=basketrecord', id: 'iframeBasketAORallwBlock', name: 'iframeBasketAORallwName', frameborder: 0, scrolling: 'no'}).appendTo("body");
  $('#iframeBasketAORallwBlock').css('position', 'fixed').css('top' , 0).css('bottom' , 0).css('left' , 0).css('right' , 0).css('width' , '100%').css('height' , '100%').css('z-index' , '999999');
  let toframeData = { 'basket_composition': $('.__alk_basket_partsworks_json').text(), 'client_info': $('.__alk_basket_clientinfo_json').text() };
  postToAORIframe(toframeData, protocolVersion+'://booking.agrad.ru/aor-load-full.php?to=basketrecord','iframeBasketAORallwName');

  setTimeout(sendPostComDatatoIFrame.bind(null, toframeData), 1000);
}


function sendPostComDatatoIFrame(strdata){
  var ifrWindow = document.getElementById('iframeBasketAORallwBlock');
  ifrWindow.contentWindow.postMessage(JSON.stringify({'postData': strdata}), "*");
}

function postToAORIframe(ThisData,url,target){
    var object = this;
    object.time = new Date().getTime();
    $('.__alk-weel').append('<form action="'+url+'" method="post" target="'+target+'" id="postToAORIframe'+object.time+'" style="display:none;" name="formPostToAORIframe'+object.time+'"></form>');
    $.each(ThisData,function(thisN,thisV){
        $('#postToAORIframe'+object.time).append("<input type='hidden' name='"+thisN+"' value='"+escQuot(thisV)+"' />");
    });
    $('#postToAORIframe'+object.time).submit().remove();
}
function escQuot(text) { return text.replace(/'/g, "\\\'"); }
function aorIFReceiveMessage(event)
{
  // trust the sender
  if (event.origin !== "http://booking.agrad.ru" &&  event.origin !== "https://booking.agrad.ru")
    return;
    

  if (event.data == 'doSuccessRecordActions')
  {
    // clear basket
    clear_my_basket();
    // goto record_list page
    openpgCurrentRecords();
  }
    
  if (event.data == 'docloseORiframe')
  {
    if ($('#iframeBasketAORallwBlock').length > 0)
    {
      $('#iframeBasketAORallwBlock').remove();
    }
  }
  
}
window.addEventListener("message", aorIFReceiveMessage, false);