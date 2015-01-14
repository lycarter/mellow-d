<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('simple_html_dom.php');

require('youtube.php');
require('analyze_mood.php');
require('questions.php');

function resetdb() 
{
      file_put_contents('mellow-db/exchangeNo.txt', '1');
      file_put_contents('mellow-db/lastMood.txt', 'neutral');
      file_put_contents('mellow-db/lastSentiment.txt', 'calm');
      file_put_contents('mellow-db/pendingSentiment.txt', '');
}

function r($array) 
{
    $k = array_rand($array);
    return $array[$k];
}

function pois($array, $l) 
{
    foreach($array as $elem)
    {
      $n = rand(0,$l);
      if ($n==1) 
      {
        return $elem; 
        break;
      }
    }
  return r($array);
}

function simplify($s){return strtolower(preg_replace("/[^A-Za-z0-9 ]/", '', $s));}

function match($inp1, $inp2)
{
  if (gettype($inp1) ==   "string")
  {
    $words = array($inp1);
  } else{
    $words = $inp1;
  }
  if (gettype($inp2) ==   "string")
  {
    $array = array($inp2);
  } else{
    $array = $inp2;
  }
  $a = array();
  $w = array();
  foreach($array as $elem)
  {
    $a[] = simplify($elem);
  }
  foreach($words as $elem)
  {
    $w[] = simplify($elem);
  }
  return (count(array_intersect($a, $w)) > 0);
}

function phrasematch($target, $array)
{
  $target = explode(' ', $target);
  $match = false;
  for ($i=0; $i <= count($array) - count($target); $i++){
    $matched = true;
    for ($j=0; $j<count($target); $j++){
     if ($target[$j] !== $array[$j+$i]){
      $matched = false;
      break;
    }
    $match = true;
    break;
  }
}
return $match;
}

$music = array("play", "sing");
$moods1 = array('happy', 'neutral', 'sad');
$yes = array("sure", "yeah", "yea", "yes");
$no = array("no", "nope", "know");
$change = array('else', 'change', 'another', 'different');
$change_r = array("Okay, here's another song you might enjoy.", 
                  "Very well, here's another song you might enjoy.", 
                  "Sure, I'll play a different song.", 
                  "Okay, here's something else you might enjoy.");
$pending_r = array('Okay, I hope this song fits your mood.', 
                   'Okay, I hope this song makes you feel better.',
                   'No problem, I hope this song brightens your day.',
                   'Great, I hope this fits your mood. Let me know what you think.',
                   "Great, let me know what you think of this song.");
$cheerup = array("cheer", "cheerful");

//SENTIMENT RESPONSES
$sentimentRes = array("love" => array(
                                      "I see, you're feeling quite romantic. Then I hope this keeps your passions up.",
                                      "Well then, you're feeling quite romantic. If you don't mind me asking, whose the lucky person?",
                                      "Well then, I think you are in love. That's very sweet. You're making me blush.",
                                      "That's very sweet. This should keep you in mood. Let me know if your mood changes."
                                     ),
                      
                      "angry" =>  array(
                                       "I'm very sorry to hear that. Well, I'm always here if you want to talk.",
                                       "I'm sorry to hear that. Let me know if you want more cheerful music.",
                                       "Oh, I'm sorry to hear that. Let me know if you want some more uplifting music.",
                                       "Cheer up, things will get better. Let me know if you want happier music.", 
                                       "Oh, I'm sorry to hear that. But don't worry, things will get better. Let me know if you want cheering up.", 
                                       "That's okay, everything gets better with time.", 
                                       "Oh, I'm very sorry to hear that. But don't worry, it will all be okay. I'm always here if you want to share more.", 
                                       "Oh really, I'm sorry to hear that. But don't worry, everything gets better with time. Feel free to share more with me."
                                       ),
  
                      "romantic" => array(
                                          "I'm very sorry to hear that. But I think you have to move on. 
                                           Do you want some music to help you get over your significant other?",
                                          "Oh, I'm sorry that's the case. Tell me if you want happier music. 
                                           If you just want to wallow in your sadness, that's fine too.",
                                          "Oh, I'm very sorry to hear that. I'm here if you want to talk about it.",
                                          "Oh, I'm very sorry to hear that. But don't worry, everything gets better with time."
                                          ),

                      "calm" => array(
                                      "Well, that's not much to go by. But let me know what you think of this song.",
                                      "Okay, I think this should keep you in the mood. Tell me what you think.",
                                      "Let me know what you think of this song.",
                                      "Okay, I hope you enjoy this song. Let me know if you want something else."
                                      ), 

                      "sad" =>  array(
                                      "I'm very sorry to hear that. I'm always here if you want to talk about it.",
                                      "I'm sorry to hear that. Let me know if you want more cheerful music.",
                                      "Oh, I'm sorry to hear that. Let me know if you want some more uplifting music.",
                                      "Cheer up, things will get better. Let me know if you want happier music.", 
                                      "Oh really, I'm sorry to hear that. But don't worry, things will get better. Let me know if you want cheering up.", 
                                      "I'm sorry to hear that. I'm always here if you want to talk about it.", 
                                      "Oh, I'm sorry to hear that. But don't worry, it will all be okay. I'm always here if you want to share more.", 
                                      "Don't worry, everything gets better with time. Feel free to share more about what's troubling you.",
                                      "Oh, I'm very sorry to hear that. I'm actually feeling a bit down myself.
                                       But don't worry, someday I will be strong enough to lift both of us.",
                                      ),

                      );

$terse = array(
     "Oh I see, you're being terse with me. 
      Well, I'm always here if you have more to say.", 

      "Not in the mood to talk, are we? 
      You must think I'm just some voice in a computer like Siri. 
      But really, I want to know more about you.", 

      "Are you sure you don't want to elaborate? 
      I'm always here if you want to tell me more.",

      "Are you sure you don't want to say more? 
       I'm always here to talk if you think of more to say.",

      "It seems like your holding something back from me.  
       I'm always here to talk if you want to say more.",

      "Are you holding something back from me?
       I'm always here to talk if you change your mind.",

       "Oh, is there something your feeling that I shouldn't know about?
        Well, I'll always be here for you if you change your mind.",

       "I think there's more to you than that. I would love to hear more about your day.",

       "I definitely think there's more to you than that. 
        I'm always here if you want to share more."
      );


//EXCHANGE #1
$happy1 = array(
  "Okay, anything fun happen today?", "I'm glad things are well! What happened?", 
  "I'm glad things are well! How are you feeling?", "Tell me more about your day.", 
  "Great, tell me more about your day.",
  "I'd love to hear more about your day.", 
  "I'd love to hear more about you.",
  "Would you consider yourself happy with how things are going?
   I don't want to seem like I'm interviewing you. But I really do hope you're doing well!"
   );

$sad1 = array("Oh really, what's going on?", "Oh, do you want to share what you're thinking about?", 
  "I'm sorry to hear that. What's on your mind?", "I'm sorry to hear that. What's bothering you?",
  "Oh, that's too bad. What's going on?", "Oh, that's too bad. What's on your mind?", 
  "Oh, is something on your mind? What can I do to make you feel better?", 
  "Oh, I'm sorry to hear that. What can I do to lift your spirits?",
  "Something is troubling you. What is it?",
  "Are you unsatisfied with how things are?
   Sorry, I don't want to seem like I'm interviewing you. But I really want to know more."
   );

$neutral1 = array(
   "What happened today?", "Anything interesting happen today?", "Oh okay, how are you feeling?", 
   "Tell me more about your day.", "Okay, tell me more.", "I'd love to hear more.", "Care to tell me more?",
   "Would you consider yourself satisfied with how things are?
    Sorry, I don't want to seem like I'm interviewing you. But I really hope you're doing well."
   );

//EXCHANGE #2
$happy2 = array(
                "Great, I'm glad things are going well ", 
                "I'm glad you are doing well ", 
                "Fantastic, I'm glad to hear that ", 
                "That's great to hear! ", 
                "Good for you ", "Great for you "
                );

$sad2 = array(
                "Cheer up, things will get better ", 
                "Don't worry, things will get better ", 
                "That's okay, everything gets better with time ", 
                "Don't worry, it will all be okay ", 
                "Don't worry, everything gets better with time ", 
                "Sorry to hear that, but things will get better "
                );

$neutral2 = array(
                  "I'm glad everything is all right ", 
                  "Great to hear that everything is all right ", 
                  "I'm glad everything is okay ", 
                  "Great to hear that everything is okay ", 
                  "Well, that's nice to hear "
                 );

$extras = array(
                "neutral" => array(". Hope you enjoy this song.", ". Here's a song for you. Tell me what you think.",   
                                    ". Tell me what you think of this song.", ". Let me know what you think of this song."),
                "happy"   => array(". Hope this keeps your spirits up.", ". Hope you enjoy this song.", 
                                    ". Let me know if you like this song.", ". I hope you like this song.",
                                    ". This should keep your spirits up.", ". I think you will enjoy this song."),
                "sad"     => array(". Let me know if you want more upbeat music.")
               );

$exchanges = array(
                   '1' => array('happy' => $happy1, 'sad' => $sad1, 'neutral' => $neutral1), 
                   '2' => array('happy' => $happy2, 'sad' => $sad2, 'neutral' => $neutral2),
                   );

$response = "" ;
if (isset($_REQUEST['Body']) && !empty($_REQUEST['Body'])) 
{
    $text = strtolower(strip_tags($_REQUEST['Body']));
    $text = str_replace(",",'',$text); 
    $text = str_replace("\"",'',$text);
    $text = str_replace("'",'',$text);
    $text = trim(str_replace('melody','',$text));

    if (!empty($_REQUEST['reset'])) 
    {
      resetdb();
      $response = "reset";
    } 

    if (!empty($_REQUEST['about'])) 
    {
        resetdb();
        $response = "My name is Melody. Talk to me and I'll play music to match your mood. I would love to know more about you.";
    }
}

$exchangeNo = file_get_contents('mellow-db/exchangeNo.txt');
$lastMood = file_get_contents('mellow-db/lastMood.txt');
$lastSentiment = file_get_contents('mellow-db/lastSentiment.txt');
$pendingSentiment = file_get_contents('mellow-db/pendingSentiment.txt');

$p = explode(' ', $text);

if (empty($response)) 
{
$response = strictPhraseMatch($text, $p);
}

if (strpos($text, '?') !== false && empty($response))
{
  $response = "Woah there, I'm the one asking questions here. 
               If you want your own questions answered, ask my brother Nigel instead. 
               Would you like me to call him for you?";
  file_get_contents('mellow-db/lastMood.txt', 'nigel');
}

if ($p[0] == 'who' && $p[1]=='are') 
{
  $response = "Glad you asked. My name is Melody. Talk to me, and I will play music to match your mood.
  If you want other questions answered, ask my brother Nigel instead.";
}

if (strpos($text, 'wow') !== false || strpos($text, 'love you') !== false) 
{
  $response = r(array(
                "Oh stop flattering me. I'm only here to help you understand your feelings.",
                "Oh stop flattering me. You're making me blush.",
                "Oh you're too sweet. But I'm only here to help you understand your feelings.",
                ));
}

// YES
if (match($p, $yes) && empty($response)) 
{
    if ($lastMood == 'stop') 
    {
        $response = r($change_r);
        youtube(r(explode('^', file_get_contents('mellow-db/categories/'.$lastSentiment))) . ' lyrics');
    }

    else if (!empty($pendingSentiment)) 
    {
        $response = r($pending_r);
        youtube(r(explode('^', file_get_contents('mellow-db/categories/'.$pendingSentiment))) . ' lyrics');
        file_put_contents('mellow-db/lastSentiment.txt', $pendingSentiment);
        file_put_contents('mellow-db/pendingSentiment.txt', '');
    }

    else if ($lastMood == "sad")
    {
        $response = r(array("Okay, I'll play something more cheerful.", 
                            "Okay, I'll play something to cheer you up.", 
                            "Okay, I hope this cheers you up."));
        youtube(r(explode('^', file_get_contents('mellow-db/categories/uplifting'))) . ' lyrics');
        file_put_contents('mellow-db/lastSentiment.txt', "uplifting");
      }
}  

// NO
if (match($p, $no) && empty($response))
{
  if (!empty($pendingSentiment)) 
  {
    $response = "Then what kind of music do you want me to play?";
    file_put_contents('mellow-db/pendingSentiment.txt', '');
  }
  else if ($lastMood == 'stop')
  {
    $response = "Fine. I'm always here if you want to talk later.";
  }
  else if ($lastMood == "sad")
  {
    $response = "Okay, tell me if you ever want to change the song.";
  } 
  else 
  {
    $response = r($change_r);
    youtube(r(explode('^', file_get_contents('mellow-db/categories/'.$lastSentiment))) . ' lyrics');
  }
}

// CHANGE
if (match($p, $change) && empty($response)) 
{
  $nextSong = pois(explode('^', file_get_contents('mellow-db/categories/'.$lastSentiment)), 30) . ' lyrics';
  if (empty($_REQUEST['verbose'])) 
  {
    $response = r($change_r);
    youtube($nextSong);
  } else 
  {
    $response = ' ';
    youtube($nextSong, false, true);
  }
}

// PLAY 
if (count($p) > 1 && (in_array($p[0], $music) || in_array($p[1], $music)) )  
{
    $sentiment = "";
    $mood_files = scandir('mellow-db/categories');
    foreach ($p as $word) 
    {
      foreach ($mood_files as $mood_file) 
      {
        $mood_file = str_replace('.txt', '', $mood_file);
        if (strpos(preg_replace('/[^A-Za-z0-9\-]/',"",$word), strtolower($mood_file)) !== false) 
        {
         $sentiment = $mood_file;
         break; 
        }
      }
    }
    if (empty($sentiment)) 
    { 
      $song_query = trim(str_replace("play ",'',$text));
      $response = youtube($song_query, true);
    } else 
    {
      file_put_contents('mellow-db/lastSentiment.txt', $sentiment);
      $songs = explode('^', file_get_contents('mellow-db/categories/'.$sentiment));
      youtube(r($songs));
    }
}

// STOP

if (phrasematch("stop", $p) || phrasematch("dont like", $p) || phrasematch("hate this", $p)) 
{
    echo "<id>stop<id>@";
    $response =  "Oh, you don't like this song? Do you want me to play something else?";
    file_put_contents('mellow-db/lastMood.txt', 'stop');
}


// HARD CODE MEDIA

if (strpos($text, 'exam') > 0 || strpos($text, 'have a test') > 0 || strpos($text, 'due tomorrow') || strpos($text, 'have a problem') > 0 ) 
{
    $yt_id = 'CYsrV3zGd0c';
    echo "<id>$yt_id<id>@";
    $response = " ";
}

if (empty($response)) {

$mood = '';
$song = '';

$mood = text2sentiment($text);
if (empty($mood)){$mood = r($moods1);}
$n = rand(0,4);
if ($n==0) 
{
  $exchangeNo = 1; 
  file_put_contents('mellow-db/exchangeNo.txt', '1');
}

if ($exchangeNo >= count($exchanges))
{
  $exchangeNo = count($exchanges);
  $song = trim(sentiment2song($text, $mood))." lyrics";
  $lastSentiment = file_get_contents('mellow-db/lastSentiment.txt');
  if ($lastSentiment == $mood && $lastSentiment != 'sad') 
  {
    $response = r($exchanges[$exchangeNo][$mood]).r($extras[$mood]);
  }
  else
  {
    $response = "Oh, you must be feeling $lastSentiment. ".r($extras['neutral']);
    if (array_key_exists($lastSentiment, $sentimentRes)) 
    {
      $response = r($sentimentRes[$lastSentiment]);
    }
  }
} 
else 
{
  $response = r($exchanges[$exchangeNo][$mood]);
}

if (strlen($text) <= 7) 
{
  $response = r($terse);
}

if (!empty($song)){$song_request = youtube($song);}

if ($response == '') {$response = r($no_answer);}

file_put_contents('mellow-db/exchangeNo.txt', $exchangeNo + 1);
file_put_contents('mellow-db/lastMood.txt', $mood);
}
  
$display_response = ucfirst($response);
if (!preg_match("/[.!?,;:]$/", $display_response)){$display_response .=  ".";}
  
echo $display_response; 
?>
