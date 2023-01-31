// Виджет feedback (iframe), Автоград
function openAFB()
{
  let protocolVersion = 'http';
  if (location.protocol == 'https:') { protocolVersion = 'https'; }
  
  $('#iframeAFBallwBlock').remove();
  // protocolVersion + '://booking.agrad.ru/feedback-index.php'
  $('<iframe>', {src: protocolVersion + '://booking.agrad.ru/feedback-index.php?fbsource='+thisfbsource, id: 'iframeAFBallwBlock', frameborder: 0, scrolling: 'no'}).appendTo("body");
  $('#iframeAFBallwBlock').css('position', 'fixed').css('top' , 0).css('bottom' , 0).css('left' , 0).css('right' , 0).css('width' , '100%').css('height' , '100%').css('z-index' , '999999');
}

function afbIFReceiveMessage(event)
{
  // trust the sender
  if (event.origin !== "http://booking.agrad.ru" &&  event.origin !== "https://booking.agrad.ru")
    return;
  if (event.data == 'docloseFBiframe')
  {
    $('#iframeAFBallwBlock').remove();
  }
}
window.addEventListener("message", afbIFReceiveMessage, false);