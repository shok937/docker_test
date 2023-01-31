// Виджет RR (from alk basket,iframe), Автоград
function openAORtoRR()
{
  let protocolVersion = 'http';
  if (location.protocol == 'https:') { protocolVersion = 'https'; }
  
  $('#iframeRRAORallwBlock').remove();
  $('<iframe>', {src: protocolVersion+'://booking.agrad.ru/aor-load-full.php?to=rerecord', id: 'iframeRRAORallwBlock', name: 'iframeRRAORallwName', frameborder: 0, scrolling: 'no'}).appendTo("body");
  $('#iframeRRAORallwBlock').css('position', 'fixed').css('top' , 0).css('bottom' , 0).css('left' , 0).css('right' , 0).css('width' , '100%').css('height' , '100%').css('z-index' , '999999');
  let toframeData = { 'rerecord_data': $('.__alk_rerecord_json').text(), 'client_info': $('.__alk_basket_clientinfo_json').text() };
  postToAORIframe(toframeData, protocolVersion+'://booking.agrad.ru/aor-load-full.php?to=rerecord','iframeRRAORallwName');

  setTimeout(sendPostComDatatoIFrameRR.bind(null, toframeData), 1000);
}

function sendPostComDatatoIFrameRR(strdata){
  var ifrWindowRR = document.getElementById('iframeRRAORallwBlock');
  ifrWindowRR.contentWindow.postMessage(JSON.stringify({'postData': strdata}), "*");
}

function postToAORIframeRR(data,url,target){
    $('body').append('<form action="'+url+'" method="post" target="'+target+'" id="postToAORIframeRR" style="display:none;"></form>');
    $.each(data,function(n,v){
        $('#postToAORIframeRR').append("<input type='hidden' name='"+n+"' value='"+escQuotRR(v)+"' />");
    });
    $('#postToAORIframeRR').submit().remove();
}
function escQuotRR(text) { return text.replace(/'/g, "\\\'"); }
function aorIFReceiveMessageRR(event)
{
  // trust the sender
  if (event.origin !== "http://booking.agrad.ru" &&  event.origin !== "https://booking.agrad.ru")
    return;
  if (event.data == 'docloseORiframe')
  {
    if ($('#iframeRRAORallwBlock').length > 0)
    {
      $('#iframeRRAORallwBlock').remove();
      // update record_list page
      openpgCurrentRecords();
    }
  }
}
window.addEventListener("message", aorIFReceiveMessageRR, false);