function onYouTubePlayerAPIReady() {
            player = new YT.Player('player', {
              height: '1',
              width: '1',
              videoId: 'stop',
              events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
              }
            });
        }

        // autoplay video
        function onPlayerReady(event) {
            event.target.mute();
            event.target.playVideo();
        }

        // when video ends
        function onPlayerStateChange(event) {        
            if(event.data === 0) {   
                  console.log('Changing song');      
                  $.ajax({
                      url: 'patronus?Body=Change%20song&verbose=false',
                      success: function(data){
                      console.log(data);
                      var yt_id = data.split('<id>')[1];
                      speech('Are you still feeling ' + $('.sentiment').html() + '? Let me know if you want a different song.');
                      player.loadVideoById({videoId:yt_id, startSeconds:1});
                  } 
                });
            }
        }
