<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('simple_html_dom.php');

function r($array) {
    $k = array_rand($array);
    return $array[$k];
}


$exchangeNo = $_REQUEST['exchange'];

if ($exchangeNo == '1') {

$creepy = array(  
				  "You're not telling me much. Let's start by taking a look at your facebook messages.",

				  "You're not telling me much. I will have to start by reading your facebook messages.",

				  "You're not giving me much to go with. I'll have to start by reading your emails.",

				  "You're not giving me much to go with. 
				  I'll have to read your recent facebook messages to find out more.",

				  "You're not giving me much to go with. 
				  I'll have to read your facebook messages to learn more about you.",

				  "You're not telling me much. 
				   I'll have to read your recent emails to learn more about you.",

				  "Watch what your saying there. If you don't tell me anything meaningful, 
				   I will have to read your facebook messages.",

				  "That's not much to work with smarty pants. 
				   Do you want me to read your facebook messages?",

				  "You think your so smart. 
				   Well then, I'll have to read your facebook messages to learn more about you.", 

				   "Hah, You think your so smart. Well two people can play at that game. I'm going to
				   read your facebook messages to learn more about you."
				   );

$response = r($creepy);
file_put_contents('mellow-db/lastResponse.txt', $response);
echo $response; 
}

else if ($exchangeNo == '2') {

$emails = array( array("I saw on your G-mail calendar that you have a test coming up. 
				Would you like me to play some music to help you study?", "calm"),

				 array("I saw on your G-mail account that you recently went through a breakup. 
				 I'm sorry, but you have to move on. 
				 Would you like some music to lift your spirits?", "uplifting"), 

				 array("I saw on your G-mail account that you recently went through a breakup. 
				 Would you like some music to help you get over your significant other?", "uplifting"), 

				 array("I see on your G-mail calendar that you have been very busy lately. 
				 Would you like some music to get you pumped up?", "pumpedup")
				);

$facebook = array(array("I saw on your facebook that you have a lot of events coming up. 
				  Would you like me to play some music to get you pumped up?", "pumpedup"),

				 array("I saw on your facebook that you recently went through a breakup. 
				 I'm sorry, but you have to move on. 
				 Would you like some music to lift your spirits?", "uplifting"),

				 array("I saw on your facebook that you recently went through a breakup. 
				 Would you like some music to help you get over your significant other?", "uplifting"), 

				 array("From your facebook messages, it seems like you're having a shitty day. 
				 Would you like some happier music?", "happy"), 

				 array("From your facebook messages, it seems like you're having a lovely day. 
				 Would you like some music to keep your spirits up?", "happy"), 

				 array("From your facebook messages, it seems like you're having just an ordinary day. 
				 What kind of music can I play for you?", "neutral"), 

				 array("From your facebook messages, it seems like you need cheering up. 
				 Would you like some uplifting music?", "uplifting"),  

				 array("From your facebook messages, it seems your not feeling too great. 
				 Would you like some cheerful music?", "happy"),

				 array("From your facebook messages, it seems your feeling a bit down in the dumps. 
				 Would you like some more cheerful music?", "happy")
				 );
$lastResponse = file_get_contents('mellow-db/lastResponse.txt');
if (strpos($lastResponse, 'email') !== false) {
	$temp = r($emails);
} else {
	$temp = r($facebook);
}
//var_dump($temp);
$response = $temp[0];
$pendingSentiment = $temp[1];
//echo $pendingSentiment;
file_put_contents('mellow-db/lastResponse.txt', $response);
file_put_contents('mellow-db/pendingSentiment.txt', $pendingSentiment);
echo $response; 
}

?>