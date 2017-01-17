<?php

$file = fopen("htmlMazzi.txt","w");
fclose($file);

$value = 'https://www.powned.it/hearthstone/listamazzi/page/';
for($counter = 1; $counter < 40; ++$counter) {
	get($value."{$counter}/");
}

function get($name)
{
	echo $name;
	$content = getWebPageContent($name);
	$content = strstr($content, '<td><a href');
	$content = strstr($content, '</table>', true);
	$f = fopen("htmlMazzi.txt","a");
	fputs($f,$content);
	fclose($f);
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
	echo $content."\n";
	
	//Printing errors
	//echo curl_error($ch);
	
	//Closing connection
	curl_close($ch);
	
	return $content;
}
?>
