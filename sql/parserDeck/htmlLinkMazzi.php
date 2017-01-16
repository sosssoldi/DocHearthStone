<?php

$file2 = fopen('datiMazzi.txt', 'w');
fclose($file2);

$file = fopen('linkMazzi.txt', 'r');
while(!(feof($file))) {
	$row = fgets($file);
	$row = substr($row, 0, -1);
	get($row);
}
fclose($file);

function get($name)
{
	echo $name;
	$content = getWebPageContent($name);
	$content_copy = $content;
	$content = strstr($content, '</h1>', true);
	$content = strstr($content, '<h1');
	$content = str_replace('<h1 id="deck-title"><img src="https://i.imgur.com/', "", $content);
	$content = str_replace('" style="height:60px;">', ";", $content);
	$content_copy = strstr($content_copy, '" style="width:100%">', true);
	$content_copy = strstr($content_copy, 'data-deck="[');
	$content_copy = str_replace('data-deck="[', "", $content_copy);
	$file2 = fopen('datiMazzi.txt', 'a');
	if($content && $content_copy) {
		fputs($file2, $content."\n");
		fputs($file2, $content_copy."\n");
		fclose($file2);
	}
}

function getWebPageContent($url)
{
	//Getting connection
	$ch = curl_init() or die(curl_error());
	
	//Setting parameters
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	//Getting content
	$content=curl_exec($ch);
	
	//Printing errors
	//echo curl_error($ch);
	
	//Closing connection
	curl_close($ch);
	
	return $content;
}
?>
