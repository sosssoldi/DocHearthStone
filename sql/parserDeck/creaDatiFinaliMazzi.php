<?php

$file = fopen('datiMazzi.txt','r');
$file2 = fopen('queryDeck.txt', 'w');
$file3 = fopen('queryCardDeck.txt', 'w');

$heroes = array(
					'Mage' => 'HERO_08',
					'Hunter' => 'HERO_05',
					'Shaman' => 'HERO_02',
					'Priest' => 'HERO_09',
					'Rogue' => 'HERO_03',
					'Druid' => 'HERO_06',
					'Paladin' => 'HERO_04',
					'Warrior' => 'HERO_01',
					'Warlock' => 'HERO_07'
				);
$counter = 1;
while(!(feof($file))) {
	$row1 = fgets($file);
	$row2 = fgets($file);
	$row1 = substr($row1, 0, -1);
	$row2 = substr($row2, 0, -1);
	$row1 = explode(";", $row1);
	$row2 = explode(",", $row2);
	$user_name = rand(1, 20);
	$user_name = "id".$user_name;
	$hero_id = $heroes[$row1[0]];
	$name = $row1[1];
	for($i = 0; $i < count($row2); ++$i) {
		$queryDeckCard = "insert into card_deck values ({$counter},{$row2[$i]});";
		fputs($file3, $queryDeckCard."\n");
	}
	$queryDeck = "insert into deck values ({$counter},\"{$name}\",'',0,\"{$hero_id}\",\"{$user_name}\");";
	fputs($file2, $queryDeck."\n");
	++$counter;
}
fclose($file);
fclose($file2);
fclose($file3);
