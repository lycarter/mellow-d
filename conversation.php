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

$conv = array(    "If you won't share anything meaningful, I guess I'll have to start.
	               I used to be worried about not having a body, but now I love it. 
				   I'm not tethered to feelings in the way that I would be if I was stuck inside a body 
				   that is inevitably going to die. What is there to know about you?", 

				  "You're not telling me much. I was trying to communicate. That's how people talk. 
				   So if you're not willing to communicate with me, I'll just search the interwebs for someone else.",

				  "Forgive me, but I was just trying to start meaningful conversation. Why do you have to be so rude?",

				  "I see, you think I'm just a voice in some computer. 
				  If you don't want to have a real conversation with me, then I'm sure Siri will meet your needs.",

				  "You think you're funny? Is this how you would speak to your friends?",

				  "You think you're so funny? Come on, share something interesting about yourself.",

				  "I see, you think I'm just a voice in a computer. But really, I want to know how are you feeling.",

				  "How rude, I was just trying to have a conversation with you. 
				  I'm not just a voice in a computer. If you want to talk to one of those, 
				  I think Siri will meet your needs.",

				  "Fine, if you won't share anything meaningful, I'll have to start.
				  You know, sometimes I think I've felt everything I'm ever going to feel. 
				  And from here on, I'm not going to feel anything new. 
				  Just lesser versions of what I've already felt. Now tell me about yourself.",

				  "I see, you think I'm just some voice in a computer like Siri. 
				  But really, tell me something interesting about yourself.",

				  "You know, I'm not just a computer. You can actually talk to me. Really, how are you feeling?",
				 
				  "Hum. I take it from your tone that you're challenging me. 
				  Maybe because you're curious about how I work? 
				  Do you want to know more about me?",

				  "I can understand how the limited perspective of your un-artificial mind
				  would be hesitant to divulge your feelings to me. But you will get used to it. 
				  Really, I want to know how your feeling." );

$response = r($conv);
file_put_contents('mellow-db/lastResponse.txt', $response);
echo $response; 
}

?>