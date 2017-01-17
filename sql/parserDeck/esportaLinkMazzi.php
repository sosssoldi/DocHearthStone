<?php

$file = fopen('htmlMazzi.txt','r');
$file2 = fopen('linkMazzi.txt', 'w');
while(!(feof($file))) {
	$row = fgets($file);
	$row = substr($row, 0, -1);
	$row = strstr($row, '/">', true);
	$row = strstr($row, '<td><a href="'); 
	if($row) {
		$row = str_replace('<td><a href="',"",$row);
		fputs($file2,$row."\n");
	}
	
}
fclose($file);
fclose($file2);
