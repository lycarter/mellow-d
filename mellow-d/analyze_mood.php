<?php


function text2sentiment($text){
    $data = array('text' => $text);  
    $url = "http://text-processing.com/api/sentiment/";
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
            ),
        );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    //echo $result;
/*
    $needle = "\"neg\": ";
    $start = strpos($result, $needle) + strlen($needle);
    $end = strpos($result, "," , $start);
    $negative = substr($result, $start, $end-$start)-0.1;

    $needle = "\"neutral\": ";
    $start = strpos($result, $needle) + strlen($needle);
    $end = strpos($result, "," , $start);
    $neutral = substr($result, $start, $end-$start);

    $needle = "\"pos\": ";
    $start = strpos($result, $needle) + strlen($needle);
    $end = strpos($result, "}" , $start);
    $positive = substr($result, $start, $end-$start)-0.1;

    //echo $url;
*/
    $needle = "\"label\": \"";
    $start = strpos($result, $needle) + strlen($needle);
    $end = strrpos($result, "\"");
    $result = substr($result, $start, $end-$start);
    $arr = array('pos' => 'happy', 'neg' => 'sad', 'neutral' => 'neutral');
    $sentiment =  $arr[$result];

    //$url = "http://72.29.29.198/updateClock.php?positive="
    //.$positive."&negative=".$negative."&neutral=".$neutral;
    //file_get_contents($url);
    //echo $url;

    return $sentiment;
}

function sentiment2song($text, $sentiment) {

    $angry = array("crap", "shit", "hate", "fuck", "fail", "damn", "mother",
        "mom");
    $angst = array("life sucks", "depress", "bad grade");
    $annoyed = array("pissed", "what the", "annoying", "upset", "can't stand",
        "cannot stand", "unbelieveable", "can't handle", "cannot handle");
    $bittersweet = array("bitter", "worst", "apart", "grudge");
    $calm = array("not much", "not too much", "cool", "peace", "fine", "not bad",
        "chill", 'not too');
    $happy = array("pretty good", "satis", "best", "eating", "food", "din",
        "breakfast", "brunch", "lunch", "cheer", "carefree", "radiant", "bounc",
        "sunny", "thank", "deli");
    $jazz = array("smooth", "relax", "sax");
    $joy = array("wonder", "amaz", "glee", "smil", "fanta", "present",
        "Christmas", "gift", "birthday");
    $longing = array("i wish", "want", "hope", "crave", "aspire", "need");
    $love = array("date", "married", "kiss", "sex", "ring", "roman", "tonight",
        "lust", "in bed", "beaut", "lovely", "heart", "only one", "together",
        "crush", "hot", "sweet");
    $melancholy = array("lost", "lonely", "alone", "depress", "cloud", "rain",
        "dreary");
    $neutral = array("okay", "meh", "tolera", "indifferent", "apath");
    $positive = array("terrific", "plus", "bright side", "better", "great");
    $powerful = array("terrib", "horrib", "would end"); // sad
    $pumped = array("i won", "winner", "workout", "good grade", "excit", 'cat ', 'kitten',
    "can't believe it", "cannot believe", "wow", "woo hoo", "ecstatic",
    "can't wait", "next house", "pump", 'aced ', 'great on a test');
    $romantic = array("broke up", 'lovesick', "romance", "cheating", "divorce", "left",
    "break", "hurt"); //sad
    $sad = array("miser", "down", "gloom", "woebegon", "cry", "dejected", "reject",
    "regret", "wish I hadn't", "sorry", "pathetic", "dumps", "piti", 'not too good');
    $spiritual = array("god", "jesus", "church", "mosque", "pray", "preach",
    "bible");


$keywords = array("angry" => $angry, "angst" => $angst, "annoyed" => $annoyed,
    "bittersweet" => $bittersweet, "calm" => $calm, "happy" => $happy,
    "jazz" => $jazz, "joy" => $joy, "longing" => $longing, "love" => $love,
    "melancholy" => $melancholy, "neutral" => $neutral,
    "positive" => $positive, "reallysad" => $powerful, 
    "romantic" => $romantic, "sad" => $sad, "spiritual" => $spiritual,
    "pumpedup" => $pumped);


foreach($keywords as $emotion => $phrases) {
	foreach($phrases as $phrase) {
		$text = str_replace($phrase, $emotion, $text);
	}
}
//echo "<br>".$text."<br>";

$p = explode(' ', $text);
$mood_files = scandir('mellow-db/categories');
foreach ($p as $word) {
  foreach ($mood_files as $mood_file) {
      $mood_file = str_replace('.txt', '', $mood_file);
      if (strpos(preg_replace('/[^A-Za-z0-9\-]/',"",$word), strtolower($mood_file)) !== false) {
         $sentiment = $mood_file;
         break;	
     }
 }
}

$color = "white";
$colors = array('angry'=>'red', 'angst' => 'red', 'sad'=>'blue', 'love'=>'purple', 'positive' => 'green',
                'romantic'=>'purple', 'happy'=>'orange', 'melancholy'=>'blue', 'joy' => 'orange');
if (array_key_exists($sentiment, $colors)){
    $color = $colors[$sentiment];
}

file_put_contents('mellow-db/lastSentiment.txt', $sentiment);
$songs = explode('^', file_get_contents('mellow-db/categories/'.$sentiment));
//$url = "http://72.29.29.198/updateClock.php?sentiment=".$sentiment;
//file_get_contents($url);


$sentiment_synonyms = array('romantic' => array('heartbroken :(', 'heartbroken', 'romantic regret', 'lovesick'),
                            'pumpedup'=> array('pumped up!', 'excited!', 'energized'), 
                            'reallysad'=> array('really sad :(', 'teribbly sad', 'afflicted', 'deeply depressed'),
                            'sad' => array('sad :(', 'sad', 'down in the dumps', 'unhappy :(', 'gloomy', 'a bit blue'),
                            'neutral' => array('quite neutral', 'neutral', 'so-so', 'calm'),
                            'angry' => array('angry', 'resentful', 'frustrated'),
                            'angst' => array('a bit angsty', 'angsty', 'some angst', 'hateful'),
                            'love' => array('in love &hearts;', 'wishy-washy love', 'in love', 'romantic', 'affectionate'),
                            'melancholy' => array('melancholic', 'nostalgic', 'pensive', 'reflective', 'lonely')
                            );
$adj = $sentiment;
if (array_key_exists($sentiment, $sentiment_synonyms)){
    $adj = r($sentiment_synonyms[$sentiment]);
}

$preface = array('You seem to be feeling ', 'You may be feeling ', "I think you're feeling ", 'I think you feel ', 
    'You seem to feel ');

echo r($preface).
     "<span style=\"color:$color\" class = 'sentiment'>"
     . strtolower($adj) . 
     "</span>. <br>";

if ($sentiment == "happy" || $sentiment == "sad" || $sentiment == "neutral" || $sentiment == "angry" ){
    return r($songs);
} else{
    return pois($songs,20);
}
//var_dump($songs);

}
?>