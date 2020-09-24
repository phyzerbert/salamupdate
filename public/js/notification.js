var notificationsWrapper   = $('.dropdown-notifications');
var notificationsToggle    = notificationsWrapper.find('a[data-toggle]');
var notificationsCountElem = notificationsToggle.find('i[data-count]');
var notificationsCount     = parseInt(notificationsCountElem.data('count'));
var notifications          = notificationsWrapper.find('ul.dropdown-menu');

// if (notificationsCount <= 0) {
//     notificationsWrapper.hide();
// }

// Enable pusher logging - don't include this in production
// Pusher.logToConsole = true;

var pusher = new Pusher('a66451918bd14635c235', {
    encrypted: true
});

// Subscribe to the channel we specified in our Laravel Event
var channel = pusher.subscribe('send-notification');

// Bind a function to a Event (the full Laravel class)
channel.bind('App\\Events\\SendNotification', function(data) {
    console.log('Notification: ', data);
    // var existingNotifications = notifications.html();
    // var avatar = Math.floor(Math.random() * (71 - 20 + 1)) + 20;
    // var newNotificationHtml = `
    //     <li class="notification active">
    //         <div class="media">
    //         <div class="media-left">
    //             <div class="media-object">
    //             <img src="https://api.adorable.io/avatars/71/`+avatar+`.png" class="img-circle" alt="50x50" style="width: 50px; height: 50px;">
    //             </div>
    //         </div>
    //         <div class="media-body">
    //             <strong class="notification-title">`+data.message+`</strong>
    //             <!--p class="notification-desc">Extra description can go here</p-->
    //             <div class="notification-meta">
    //             <small class="timestamp">about a minute ago</small>
    //             </div>
    //         </div>
    //         </div>
    //     </li>
    // `;
    // notifications.html(newNotificationHtml + existingNotifications);
    // notificationsCount += 1;
    // notificationsCountElem.attr('data-count', notificationsCount);
    // notificationsWrapper.find('.notif-count').text(notificationsCount);
    // notificationsWrapper.show();
});