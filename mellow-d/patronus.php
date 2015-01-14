<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('simple_html_dom.php');

require('youtube.php');
require('analyze_mood.php');

function r($array) {
    $k = array_rand($array);
    return $array[$k];
}

function pois($array, $l) {
    foreach($array as $elem){
      $n = rand(0,$l);
      if ($n==1) {
        return $elem;
        break;
      }
  }
}

function simplify($s){
  return strtolower(preg_replace("/[^A-Za-z0-9 ]/", '', $s));
}

function match($inp1, $inp2){
  if (gettype($inp1) ==   "string"){
    $words = array($inp1);
  } else{
    $words = $inp1;
  }
  if (gettype($inp2) ==   "string"){
    $array = array($inp2);
  } else{
    $array = $inp2;
  }
  $a = array();
  $w = array();
  foreach($array as $elem){
    $a[] = simplify($elem);
  }
  foreach($words as $elem){
    $w[] = simplify($elem);
  }
  return (count(array_intersect($a, $w)) > 0);
}

function phrasematch($target, $array){
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
$no_answer = array("Sorry, I didn't quite get that.", "I didn't quite catch that.", "I'm not sure I understand you.");
$moods1 = array('happy', 'neutral', 'sad');
$yes = array("sure", "yeah", "yea", "yes");
$no = array("no", "nope", "know");
$change = array('else', 'change', 'another', 'different');
$change_r = array("Okay, here's another song.", "Very well, I'll play another song for you.", 
  "Sure, I'll play a different song.", "Okay, here's something else you might enjoy.");
$cheerup = array("cheer", "cheerful");
//EXCHANGE #1
$happy1 = array('Okay, anything fun happen today?', "I'm glad things are well! What happened?", 
  "I'm glad things are well! How are you feeling?", "Tell me more about your day.", "Great, tell me more.",
  "I'd love to hear more about your day.", "I'd love to hear more.");
$sad1 = array("Oh really, what's going on?", "Oh, do you want to share what you're thinking about?",  
  "Oh, that's too bad. Please tell me more.", "Oh, I'm sorry to hear that. Tell me more.", 
  "Oh, is something bothering you?");
$neutral1 = array("What happened today?", "Anything interesting happen today?", "Oh okay, how are you feeling?", 
   "Tell me more about your day.", "Okay, tell me more.", "I'd love to hear more.", "Care to tell me more?");

//EXCHANGE #2
$happy2 = array("Great, I'm glad things are going well ", "I'm glad you are doing well ", 
  "Fantastic, I'm glad to hear that ", "That's great to hear! ", "Good for you ", "Great for you ");
$sad2 = array("Cheer up, things will get better ", "Don't worry, things will get better ", 
  "That's okay, everything gets better with time ", "Don't worry, it will all be okay ", 
  "Don't worry, everything gets better with time ", "Sorry to hear that, but things will get better ");
$neutral2 = array("I'm glad everything is all right ", "Great to hear that everything is all right ", 
  "I'm glad everything is okay ", "Great to hear that everything is okay ", "Well, that's nice to hear ");

$extras = array("neutral" => array(". Hope you enjoy the music.",   ". Here's a song for you. Tell me what you think.",   
                                    ". Tell me what you think of this song.", ". Let me know what you think of this song."),
                "happy"   => array(". Hope this keeps your spirits up.", ". Hope you enjoy the music.", 
                                    ". Let me know if you like this song.", ". I hope you like this song.",
                                    ". This should keep your spirits up.", ". I think you will enjoy this song."),
                "sad" => array(". Let me know if you want more upbeat music.")
                );

$exchanges = array(
                   '1' => array('happy' => $happy1, 'sad' => $sad1, 'neutral' => $neutral1), 
                   '2' => array('happy' => $happy2, 'sad' => $sad2, 'neutral' => $neutral2),
                   );

$exchangeNo = file_get_contents('mellow-db/exchangeNo.txt');
$lastMood = file_get_contents('mellow-db/lastMood.txt');
$lastSentiment = file_get_contents('mellow-db/lastSentiment.txt');

$response =   "" ;
if (isset($_REQUEST['Body']) && $_REQUEST['Body'] !== '') {
    $text = strtolower(strip_tags($_REQUEST['Body']));
    $text = str_replace(",",'',$text); 
    $text = str_replace("\"",'',$text);
    $text = str_replace("'",'',$text);
    $text = trim(str_replace('nigel','',$text));
    if (!empty($_REQUEST['reset'])) {
      file_put_contents('mellow-db/exchangeNo.txt', '1');
      $response = "reset";
    } 
}
$p = explode(' ', $text);

if (strpos($text, '?') !== false){
  $response = "I'm the one asking questions here. If you want your own questions answered, ask my brother Nigel instead. Would you like me to call him for you?";
  file_get_contents('mellow-db/lastMood.txt', 'nigel');
}

if ($p[0] == 'who' && $p[1]=='are') {
  $response = "Glad you asked. My name is Melody. Tell me how you are feeling, and I will try to play music to match your mood.
  If you want other questions answered, you should ask my brother Nigel instead.";
}

if (match($p, $yes)) {
  if ($lastMood == "sad"){
    $response = r(array("Okay, I'll play something more cheerful.", 
                        "Okay, I'll play something to cheer you up.", 
                        "Okay, I hope this cheers you up."));
    youtube(r(explode('^', file_get_contents('mellow-db/categories/uplifting'))));
  }
if ($lastMood == 'stop') {
      $response = r($change_r);
      youtube(r(explode('^', file_get_contents('mellow-db/categories/'.$lastSentiment))));
  }
} 

if (match($p, $no)){
  if ($lastMood == "sad"){
    $response = "Okay, tell me if you ever want to change the song.";
  }
  if ($lastMood == "neutral") {
    $response = r($change_r);
    youtube(r(explode('^', file_get_contents('mellow-db/categories/'.$lastSentiment))));
  }
  if ($lastMood == 'stop'){
    $response = "Fine. I'm always here if you want to talk later.";
  }
}

if (match($p, $change)) {
  $nextSong = r(explode('^', file_get_contents('mellow-db/categories/'.$lastSentiment)));
  if (empty($_REQUEST['verbose'])) {
  $response = r($change_r);
  youtube($nextSong);
  } else {
    $response = ' ';
    youtube($nextSong, false, true);
  }
}


if (count($p) > 1 && (in_array($p[0], $music) || in_array($p[1], $music)) )  {
    $song_query = trim(str_replace("play ",'',$text));
    $response = youtube($song_query, true);
}


if ($p[0] ==  "shut" || phrasematch('stop', $p) || phrasematch("don't like", $p) ) {
    $yt_id = 'stop';
    echo "<id>stop<id>@";
    $response =  "Oh, you don't like this song? Do you want me to play something else?";
    file_put_contents('mellow-db/lastMood.txt', 'stop');
}


// HARD CODE MEDIA

  if (strpos($text, 'exam') > 0 || strpos($text, 'have a test') > 0 || strpos($text, 'due tomorrow') || strpos($text, 'have a problem') > 0 ) {
    $yt_id = 'CYsrV3zGd0c';
    echo "<id>$yt_id<id>@";
    $response = " ";
  }

if (empty($response)) {

$mood = '';
$song = '';

$mood = text2sentiment($text);
if (empty($mood)){
  $mood = r($moods1);
}

if ($exchangeNo >= count($exchanges)){
  $exchangeNo = count($exchanges);
  $song = trim(sentiment2song($text, $mood))." lyrics";
  if (empty($song)) { 
    $song = r($songs_r)." lyrics";
  }
  $response = r($exchanges[$exchangeNo][$mood]).r($extras[$mood]);
} else {
  $response = r($exchanges[$exchangeNo][$mood]);
}

if (!empty($song)){
  $song_request = youtube($song);
}

if ($response == '') {
  $response = r($no_answer);
  if (!empty($id)){
    $response =   "Sorry ".$id.", ".$response;
  }
}
file_put_contents('mellow-db/exchangeNo.txt', $exchangeNo + 1);
file_put_contents('mellow-db/lastMood.txt', $mood);
}
  
  $display_response = ucfirst($response);
  if (!preg_match("/[.!?,;:]$/", $display_response)){
    $display_response .=   ".";
  }
  echo $display_response; 
?>
