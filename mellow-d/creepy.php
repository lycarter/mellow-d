<?php

function r($array) {
    $k = array_rand($array);
    return $array[$k];
}

$exchangeNo = $_REQUEST['exchange'];

if ($exchangeNo == '1') {

$creepy = array(  "You're not telling me much. Let me take a look at your emails.", 

				  "You're not telling me much. Let's start with your emails.",

				  "You're not telling me much. Let's start with your facebook messages.",

				  "You're not giving me much to go with. Let's start with your emails.",

				  "You're not giving me much to go with. Let's start with your recent facebook messages.",

				  "You're not giving me much to go with. 
				  I'll have to read your facebook messages to learn more about you.",

				  "You're not telling me much. 
				  I'll have to read your recent emails to learn more about you.",

				  "Hum. I take it from your tone that you're challenging me. 
				  Maybe because you're curious how I work? 
				  Do you want to know more about how I work?",

				  "I can understand how the limited perspective of your un-artificial mind
				  would be hesitant to divulge your feelings to me. But you'll get used to it." );

$response = r($creepy);
file_put_contents('mellow-db/lastResponse.txt', $response);
echo $response; 
}

else if ($exchangeNo == '2') {

$emails = array("I saw on your calendar that you have a test coming up. 
				Would you like me to play some music to help you study?",

				"I saw on your emails that you recently went through a breakup. 
				 I'm sorry, but you have to move on. 
				 Would you like some music to lift your spirits?", 

				 "I saw on your emails that you went through a breakup. 
				 Would you like some music to help you get over her?");

$facebook = array("I saw on your calendar that you have a test coming up. 
				  Would you like me to play some music to help you study?",

				"I saw on your facebook that you recently went through a breakup. 
				 I'm sorry, but you have to move on. 
				 Would you like some music to lift your spirits?", 

				 "I saw on your facebook that you went through a breakup. 
				 Would you like some music to help you get over her?");

$response = r(r(array($facebook, $emails)));
file_put_contents('mellow-db/lastResponse.txt', $response);
echo $response; 
}

?>