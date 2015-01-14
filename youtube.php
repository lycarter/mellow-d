<?php
function youtube($song, $repeat = false, $id = false){
	$response = '';
	$query = '';
	$query .= $song;
	$query = str_replace(' ','+',$query);
	$youtube = "http://www.youtube.com/results?search_query=";
	$yt_url = $youtube.$query;
	$html = file_get_contents($yt_url);
	$needle = "class=\"yt-lockup-title\"><a href=\"/watch?v=";
	$start = strpos($html, $needle) + strlen($needle);
	$yt_id = substr($html, $start, 11);
    $data = "<iframe id=\"ytplayer\" type=\"text/html\" width=\"1\" height=\"1\" src=\"https://www.youtube.com/embed/"
            .$yt_id."?autoplay=1&start=1\" frameborder=\"0\" allowfullscreen>";
    $song = str_replace("lyrics", "", $song);
    if (!$id){
    if ($repeat) {
        echo "<span class = 'currSong' style=\"font-size:20pt\"> &#9834; Playing ".trim($song)." &#9834;</span>@";
        echo "<id>$yt_id<id>@";
        return "Very well. Playing ".$song.". Enjoy!";
    } else {
        echo "<span class = 'currSong' style=\"font-size:20pt\"> &#9834; Playing ".trim($song)." &#9834;</span>@";
        echo "<id>$yt_id<id>@";
        return "";
    }
    } else {
        echo "&#9834; Playing ".trim($song)." &#9834; @";
        echo "<id>$yt_id<id>";
    }
}
?>