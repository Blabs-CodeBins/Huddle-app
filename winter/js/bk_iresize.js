var EventSource;var EventOrigin=location.origin;var message;window.addEventListener('message',ReceiveMessage,!1);function ReceiveMessage(event){"use strict";EventSource=event.source;EventOrigin=event.origin;message=document.getElementById("master").scrollHeight;event.source.postMessage(message,event.origin)}
function SendMessage(i){"use strict";EventSource.postMessage(i,EventOrigin)}
function AdjustIframeHeight(i){"use strict";SendMessage(i)}
function onElementHeightChange(elm,callback){"use strict";var lastHeight=elm.clientHeight,newHeight;(function run(){newHeight=elm.clientHeight;if(lastHeight!==newHeight){callback()}
lastHeight=newHeight;if(elm.onElementHeightChangeTimer){clearTimeout(elm.onElementHeightChangeTimer)}
elm.onElementHeightChangeTimer=setTimeout(run,200)})()}
onElementHeightChange(document.body,function(){"use strict";if(window.self!==window.top){AdjustIframeHeight(document.getElementById("master").scrollHeight)}})