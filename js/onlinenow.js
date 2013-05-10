var FACEBOOK_APP_ID = '143568755827293';
var FACEBOOK_CALLBACK_URL = '//onlinenowdev.aws.af.cm';
var FACEBOOK_CANVAS_URL_RAW = 'http%3A%2F%2Fapps.facebook.com%2Fonlinenowdev';

/* Facebook JS */
FB.init({
  appId      : FACEBOOK_APP_ID,
  channelUrl : FACEBOOK_CALLBACK_URL + '/channel.html',
  status     : false
});
/* AJAX load friends */


var loadFriends = function() {
  FB.getLoginStatus(function(response) {
    if (response.status === 'connected') {
      // the user is logged in and has authenticated the app
      
      $.getJSON('https://graph.facebook.com/fql?q=SELECT%20uid%2C%20name%2C%20pic_square%2C%20online_presence%2C%20username%20FROM%20user%20WHERE%20(uid%20IN%20(SELECT%20uid2%20FROM%20friend%20WHERE%20uid1%20%3D%20me())%20AND%20(online_presence%3D%22active%22%20OR%20online_presence%3D%22idle%22))%20ORDER%20BY%20first_name&access_token=' + response.authResponse.accessToken, function(data) {
        
        // generate HTML list of friends
        var friends = [];
        $.each(data.data, function(key, val) {
          friends.push('<a href="//www.facebook.com/' + val.username + '" target="_blank"><img src="' + val.pic_square + '" class="pic" alt="' + val.name + '"><img src="/img/' + val.online_presence + '.png" class="status" alt="' + val.online_presence + '"><span class="name">' + val.name + '</span></a>');
          //if(key % 70 == 0)
          //  friends.push(LSM_Slot({adkey: '21b', ad_size: '728x90', slot: 'slot25079'}));
        });
  
  
        // fade out and clear old results
        $('#friends').css({opacity: 0});
        $('#friends').html("");
        
        
        // figure out count verbiage with plurals and such
        var count_verbiage = "";
        if(data.data.length > 0)
          count_verbiage += data.data.length;
        else
          count_verbiage += "no";
        count_verbiage += " friend";
        if(data.data.length == 0 || data.data.length > 1)
          count_verbiage += "s";
        
        
        // add new result HTML
        $('<div/>', {
          'id': 'count',
          html: "You have " + count_verbiage + " online."
        }).appendTo('#friends');
        $('#friends').append(friends.join(''));
        
        
        // fade in new results
        $('#friends').animate({opacity: 100}, 2000);
        
        
        // resize FB canvas
        FB.Canvas.setSize();
        
      });
    } else {
      
      // the user is logged in to Facebook, but has not authenticated app
      
      top.location.href = 'https://www.facebook.com/dialog/oauth?client_id=' + FACEBOOK_APP_ID + '&redirect_uri=' + FACEBOOK_CANVAS_URL_RAW + '&scope=friends_online_presence';
      
    }
  });
};

loadFriends();

/*  $(document).ready(function() {
  FB.Canvas.setSize();
});*/

// reload friends every x seconds
setInterval(loadFriends, 15000);