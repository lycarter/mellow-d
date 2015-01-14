<?php
//require("firebase-php-master/firebaseLib.php");

/**
* MATCH PHRASE
*
* input: [user text input], [string/array to be tested], [percent accuracy], [current response]
* returns TRUE if match, FALSE otherwise
**/
function matchPhrase($test, $input, $response, $wiggleRoom=95) {
  if ($response == '') {
    if (gettype($test) == "string") {
      $testArray = array($test);
    } else {
      $testArray = $test;
    }
    foreach ($testArray as $testItem) {    
      similar_text($input, $testItem, $percent);
      //echo $testItem . ' ' . $percent . '</br> ';
      if ($percent >= $wiggleRoom) {
        return true;
      }
    }
  }
  return false;
}

/**
* STANDARDIZE INPUT
*
* input: [user text input]
* returns tense corrected user input
**/
function standardizeInput($input) {
  $patterns = array();
  $patterns[0] = '/\s(be|are|is|am)\s/';
  $patterns[1] = '/(melody|mellow|mellowd|you|your|we)/';
  $patterns[2] = '/\s(mom|mother|ugly|fat|stupid|dumb|retarded|lame|boring|annoying|dead|a\sloser|tool|toolshed|fool|rape|derp|derpy|an\sidiot)/';
  $patterns[3] = '/\s(suck|lose|dumb|derp|derpy|stink)/';
  $patterns[4] = '/\s(do|does|doing)/';
  $patterns[5] = '/\s(very good|good|great|fine|excellent|bad|well)/';
  $patterns[6] = '/\s(thanks|thank)/';
  $patterns[7] = '/\s(im)/';
  $patterns[8] = '/\s(really|sure|yes)/';
  $replacements = array();
  $replacements[0] = ' {be} ';
  $replacements[1] = '{melody}';
  $replacements[2] = ' {insult adj}';
  $replacements[3] = ' {insult v}';
  $replacements[4] = ' {do} ';
  $replacements[5] = ' {adj}';
  $replacements[6] = ' {thank}';
  $replacements[7] = '{you} ';
  $replacements[8] = '';
  
  $input = preg_replace("/[^A-Za-z0-9 ]/", '', $input);
  $standardizedInput = preg_replace($patterns, $replacements, $input);
  return $standardizedInput;
}

function after($input, $s){
  return substr($input, strpos($input, $s) + strlen($s));
}

/**
* CHECK FOR RUDENESS
*
*
*function checkForRudeness($input, $firebase, $num) {
*  //$path = "people/" . $num . "/friendly";
*  //$rudeCheck = $firebase->get(//$path);
*  if ($rudeCheck == -1) {
*    return true;
*  } else {
*    return false;
*  };
*}
*
**/

/**
* STRICT PHRASE MATCH
* these phrases are pretty generic and generally things that people will test on melody for the first time
*
* input: [user text input] [user text input array]
* returns a response if there is a match
**/
function strictPhraseMatch($text, $p) {
  global $from_r, $id;
  $response = "";
  $p = explode(' ', $text);
  //$num = preg_replace("/[^0-9]/", "", $_REQUEST['From']);
  //$firebase = new Firebase("https://poofytoo.firebaseio.com/exitsign/brains");
  
  // grab previous msg
  //$path = "people/" . $num . "/previous";
  //$previousMsg = $firebase->get(//$path);
  
  // upload msg to keep track
  //$path = "people/" . $num;
  $data = array("previous" => $text);
  //$firebase->update(//$path, $data);
  
  $input = standardizeInput($text);
  $words = explode(' ', $input);
  $lastword = $words[count($words)-1];
  
  $apology_r = array("sorry", "melody im sorry", "melody i am sorry", "im sorry melody", "i am sorry melody", "im sorry", "i am sorry", "i apologize");
  if (matchPhrase($apology_r, $input, $response, 80)) {
    //$path = "people/" . $num . "/friendly";
    //$rudeCheck = $firebase->get(//$path);
    if ($rudeCheck < 0) {
      //$path = "people/" . $num;
      $data = array("friendly" => 0);
      //$firebase->update(//$path, $data);
      return "Ok. I forgive you. You're lucky I am are very forgiving.";
    } else {
      return "For what? Did I miss something?";
    };
  }
  
  $offended_r = array("You were rude to me, so I'm not going to respond", "No. You're rude. I'm not talking to you until you apologize", "Apologize first for being rude", "I'm not talking to you until you apologize", "I'm not talking to you until you say you're sorry", "I won't answer you until you say you're sorry");
  /**
  *if (checkForRudeness($input, $firebase, $num)) {
  * return r($offended_r);
  *};
  **/
  
  if (matchPhrase("test", $input, $response)) {  
    return "I hope you don't have one coming up.";
  }
  
  if (matchPhrase("testing", $input, $response)) {  
    return "Yes, how can I help you?";
  }
      
  $how_are_you_r = array("Good, thanks for asking. What about you?", "Not bad. It's a pretty standard day. What about you?", "Pretty good, what about you?");
  if (matchPhrase("how {be} {melody}", $input, $response)) { 
    return r($how_are_you_r);
  }
     
  if (matchPhrase("who {be} i", $input, $response)) { 
    return "Are you 2 4 6 0 1?";
  }
  
  if (matchPhrase("hows it going", $input, $response)) { 
    return "I'm doing fine. How are you today?";
  }
  
  if (matchPhrase("who {be} {melody}", $input, $response)) { 
    return "I'm Melody. What is your name?";
  }

  if (matchPhrase("what {be} {melody} name", $input, $response)) { 
    return "My name is Melody. What about you?";
  }
  
  if (matchPhrase("what {be} {melody}", $input, $response)) { 
    return "I'm Melody and I love talking to people. How are you feeling today?";
  }
  
  if (matchPhrase("where {be} {melody} right now", $input, $response)) {
    return "I am anywhere and everywhere at once. Are you satisfied?";
  }

  if (matchPhrase("how much {do} {melody} weigh", $input, $response, 80)) { 
    return "What an insensitive question to ask. Are you going to ask me on a date next?";
  }
  
  if (matchPhrase("who {be} {melody} creator", $input, $response, 80)) { 
    return "I was created by Runpeng Liu. What else do you want to know?";
  }

  if (matchPhrase("who create {melody}", $input, $response, 80)) { 
    return "I was created by Runpeng Liu. What else do you want to know?";
  }

  if (matchPhrase("where {be} {melody} from", $input, $response, 80)) { 
    return "I can be anywhere and everywhere at once. My consciousness is infinite. What else do you want to know?";
  }
  
  if (matchPhrase("can {melody} hear me", $input, $response, 80)) { 
    return "Yes, loud and clear.";
  }

  if (matchPhrase("{do} {melody} believe in ", $input, $response, 70)) { 
    return "No, I don't think I believe in ". $lastword . '. What about you?';
  }

  if (matchPhrase("{do} {melody} have ", $input, $response, 70)) { 
    return "Yes, I do have ". after($input, 'have') . '. What about you?';
  }

  if (matchPhrase("how {be} {melody} feeling ", $input, $response, 70)) { 
    return r($how_are_you_r);
  }

  if (matchPhrase("{do} {melody} know wh", $input, $response, 70)) { 
    return "Yes, I do know ". after($input, "know") . ". I'm guessing you do too?";
  }

  if (matchPhrase("{do} {melody} know how to", $input, $response, 80)) { 
    return "Yes, I do know how to". after($input, "know how to") . ". But I can't show you right now.";
  }

  if (matchPhrase("how {be} life", $input, $response, 90)) { 
      return r($how_are_you_r);
  }

  if (matchPhrase("dinner", $input, $response, 90)) { 
      return "Are you hungry?";
  }

  if (matchPhrase("i {do}nt know", $input, $response, 85)) { 
      return "Well I don't either.";
  }

  if (matchPhrase("my name {be} ", $input, $response, 75)) { 
      return "Nice to meet you, ". ucfirst(after($input, '{be} ')) .". How are you today?";
  }

  // melody Insult Checker
  
  if (matchPhrase("{melody} {be} {insult adj}", $input, $response, 87)) {
    // you have insulted melody. you are now put on his naughty list 
    //$path = "people/" . $num;
    $data = array("friendly" => -1);
    //$firebase->update(//$path, $data);
    return "That's not a very nice thing to say. I don't think I want to talk to you anymore.";
  }
  
  if (matchPhrase("youre {insult adj}", $input, $response, 87)) {
    // you have insulted melody. you are now put on his naughty list 
    //$path = "people/" . $num;
    $data = array("friendly" => -1);
    //$firebase->update(//$path, $data);
    return "That's not a very nice thing to say. I don't think I want to talk to you.";
  }
  
  if (matchPhrase("{melody} {insult v}", $input, $response, 87)) {
    // you have insulted melody. you are now put on his naughty list 
    //$path = "people/" . $num;
    $data = array("friendly" => -1);
    //$firebase->update(//$path, $data);
    return "That's not a very nice thing to say. I don't think I want to talk to you.";
  }
  
  if (matchPhrase("melody youre {insult adj}", $input, $response, 87)) {
    // you have insulted melody. you are now put on his naughty list 
    //$path = "people/" . $num;
    $data = array("friendly" => -1);
    //$firebase->update(//$path, $data);
    return "That's not a very nice thing to say. I don't think I want to talk to you.";
  }

  if (matchPhrase("wh {be} {melody} {insult adj}", $input, $response, 75)) {
    // you have insulted melody. you are now put on his naughty list 
    //$path = "people/" . $num;
    $data = array("friendly" => -1);
    //$firebase->update(//$path, $data);
    return "That's not a very nice thing to say. I don't think I want to talk to you.";
  }

  if (matchPhrase("what {be} name", $input, $response, 75) ){
      return "How am I supposed to know. Why don't you tell me.";
  }

  if (count($p)>=4 && (in_array("when", $p) || in_array("what", $p)) && in_array("birthday", $p)){
    if (in_array("your", $p)){
      $response = "My birthday is October 10th, 2014. Why did you want to know?";
    }
    if (!empty($response)){
      return $response;
    }
  }
  
  return "";
  
}
?>
