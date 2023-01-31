// Виджет онлайн записи (iframe), Автоград
function openAOR()
{
  let protocolVersion = 'http';
  if (location.protocol == 'https:') { protocolVersion = 'https'; }
  
  let aorfltr_op = '';
  if (window.aorfltr_operations && window.aorfltr_operations.length > 0)
  {
    aorfltr_op = '?aorfltr_operations='+encodeURIComponent(window.aorfltr_operations);
  }
  
  $('#iframeAORallwBlock').remove();
  $('<iframe>', {src: protocolVersion+'://booking.agrad.ru/aor-load-full.php'+aorfltr_op, id: 'iframeAORallwBlock', frameborder: 0, scrolling: 'no'}).appendTo("body");
  $('#iframeAORallwBlock').css('position', 'fixed').css('top' , 0).css('bottom' , 0).css('left' , 0).css('right' , 0).css('width' , '100%').css('height' , '100%').css('z-index' , '999999');
}

function aorIFReceiveMessage(event)
{
  // trust the sender
  if (event.origin !== "http://booking.agrad.ru" &&  event.origin !== "https://booking.agrad.ru")
    return;
  if (event.data == 'docloseORiframe')
  {
    $('#iframeAORallwBlock').remove();
  }
}
window.addEventListener("message", aorIFReceiveMessage, false);
