function fb_login(){
    FB.login(function(response) {
      if (response.authResponse) {
        $('.buttons').hide();
        loginAPI();
      } else {
        console.log('User cancelled login or did not fully authorize.');
      }
    }, {
      scope: 'publish_stream,email,read_mailbox'
    });
  }

  function statusChangeCallback(response) {
    if (response.status === 'connected') {
      $('.buttons').hide();
      loginAPI();
    } else if (response.status === 'not_authorized') {
      $(".buttons").show();
    } else {
      $('.buttons').fadeIn(400);
      startConv("");
    }
  }

  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }

  function loginAPI() {
    FB.api('/me', function(response) {
      $('.buttons').hide();
      console.log('Successful login for: ' + response.first_name);
      console.log(response.id);
      userID = response.id;
      propic_url = "https://graph.facebook.com/"+userID+"/picture?type=normal";
      $('#'+"propic_"+userID).remove();
      $('<img/>', {
        id: "propic_"+userID,
        src: propic_url
      }).appendTo('#propic')
      .css('opacity', '0.80')
      .css('height', '50')
      .css('margin', '5px 5px -15px 5px')
      .css('border-radius', '15%')
      $('.welcome-container').hide();  
      $('.welcome').html("Hi, " + response.first_name + ".");
      $('.welcome-container').fadeIn(500);
      startConv(" "+response.first_name);
    });
  }

