var base_url = window.location.origin;
function getContacts() {

    var us_id = $('#loggedUserId').val();
    var gmt = -3;
    var ajaxUrl = base_url+"/produccion_temporal/webchat/WatiApi/getContacts";
    $.ajax({
        url: ajaxUrl,
        type: 'post',
        data: {
            contactlistfilterid: 1,
            us_id: us_id,
            gmt: gmt,
        },
        dataType: 'html',
        success: function(response) {
            $('body').append(response);
            $('.user_list_chatbox').remove();
            var notify = $('#notfiy').val();
            var messagePreview = $('#messagePreview').val();
            var total_unread_messages = $('#total_unread_messages').val();
            if (total_unread_messages > 0) {
                $(document).prop('title', '(' + total_unread_messages + ') Forza');
            } else {
                $(document).prop('title', 'Forza');
            }
            if (notify > 0) {
                console.log('notify ' + notify);
                if (notify == 1) {
                    $.playSound('swf/notification.mp3');
                        notifyMe(messagePreview);
                } else {
                    for (var i = 0; i < notify; i++) {
                        $.playSound('swf/notification.mp3');
                            notifyMe(messagePreview);
                    }
                }
            }
            $('#messagePreview,#notfiy,#areyouthere,#total_unread_messages').remove();
        }
    });
}
getContacts();
setInterval(function () { getContacts(); }, 5000);


var hidUrl = $('#hidUrl').val();
var wp_url = base_url+'/'+hidUrl+'/boot.php?c=whatsApp';
function notifyMe(text) {

 if (Notification.permission !== 'granted')
  Notification.requestPermission();
 else {
   
   var notification = new Notification('Nuevo Mensaje', {
   
   body: text,
   icon: 'https://localizar-t.com.ar/assets/images/forza.png',
   image: 'https://localizar-t.com.ar/assets/images/forza.png'
  
  });

  notification.onclick = function() {
    window.open(wp_url+'?us_id='+us_id+'&gmt='+gmt);
  };

  setTimeout(() => notification.close(), 3000);
 
 }
}
