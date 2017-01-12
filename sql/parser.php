<?php
	$filenames = array('card1.json', 'card2.json', 'card3.json', 'card4.json', 'card5.json', 'card6.json');
	$file2 = fopen("cardparsed.txt", "a");
	$cont = 0;
	while($cont < count($filenames))
	{
		$file = fopen($filenames[$cont],"r");
		while(!feof($file)) {
			$row = fgets($file);
			$row = substr($row, 0, -2);
			$row = substr($row, 1);
			explodebycolon(explode(',', $row), $file2);
		}
		fclose($file);
		$cont++;
	}
	//Functions
	function explodebycolon($array, $f) {
	    //WILD -> NAXX, GVG
	    $heroes = array('ROGUE', 'MAGE', 'WARRIOR', 'HUNTER', 'PALADIN', 'WARLOCK', 'PRIEST', 'DRUID', 'SHAMAN');
	    $heroes_id = array('HERO_03', 'HERO_08', 'HERO_01', 'HERO_05', 'HERO_04', 'HERO_07', 'HERO_09', 'HERO_06', 'HERO_02');
	    $adv = array('BRM', 'LOE', 'KARA');
		$data = array(15); // ID,NAME,IMAGE,DESCR,RARITY,TYPE,RACE,(GOLDEN),WILD,ATTACK,HEALTH,MANA,ADV_WING,ADV_NAME,EXPANSION,HERO_ID
		for($i = 0; $i < 15; ++$i)
			$data[$i] = "";
		for($i = 0; $i < count($array); ++$i) {
			$val = $array[$i];
			$val0 = strstr($val, ':', true);
			$val1 = strstr($val, ':');
			if($val0 != "") {
				$val0 = str_replace("\"", "", $val0);
				$val1 = substr($val1, 1);
				$val1 = str_replace("\"", "", $val1);
				$val1 = str_replace("'", "\'", $val1);
				switch($val0) {
					case 'id':
						        $data[0] = "'".$val1."'";
						        $data[2]= "'".$val1.".png'";
							break;
					case 'name':
						        $data[1] = "'".$val1."'";
							break;
					case 'text':
						        $data[3] = "'".$val1."'";
							break;
					case 'rarity':
						        $data[4] = "'".$val1."'";
							break;
					case 'type':
						        $data[5] = "'".$val1."'";
							break;
					case 'race':
						        $data[6] = "'".$val1."'";
							break;
					case 'set':
						        if($val1 == 'NAXX' || $val1 == 'GVG')
						            $data[7] = "'"."TRUE"."'";
						        else
						            $data[7] = "'"."FALSE"."'";
						        for($j = 0; $j < count($adv); ++$j)
						            if($adv[$j] == $val1) {
						                $data[12] = "'".$val1."'";
						                $data[11] = "'replaceable'";   
						            }
						        $data[13] = "'".$val1."'";
						    break;
					case 'attack':
						        $data[8] = $val1;
							break;
					case 'health':
						        $data[9] = $val1;
							break;
					case 'cost':
						        $data[10] = $val1;
							break;
					case 'playerClass':
						        $data[14] = "'".$val1."'";
							break;
					case 'dust':
							break;
				}
			}
		}
		for($k = 0; $k < 15; ++$k)
			if($data[$k] == "")
				$data[$k] = "NULL";
		$str =  implode(',', $data);
		if($data[5] == "'HERO'")
		{
			/*$fh = fopen("herocard.txt","a");
			fputs($fh, $str."\n");
			fclose($fh);*/
		}
		else
		{
			$fh = fopen("hero_card_relation.txt","a");
			if($data[14] == "'NEUTRAL'")
			{
				unset($data[14]);
				$str = implode(',', $data);
				fputs($f, "insert into card values (".$str.");\n");
				for($index = 0; $index < count($heroes); ++$index) {
					fputs($fh, "insert into hero_card values ('".$heroes_id[$index]."',".$data[0].");\n");
				}
			}
			else
			{
				$h = $data[14];
				unset($data[14]);
				$str = implode(',', $data);
				for($index = 0; $index < count($heroes); ++$index)
					if("'".$heroes[$index]."'" == $h) {
						fputs($f, "insert into card values (".$str.");\n");
						fputs($fh, "insert into hero_card values ('".$heroes_id[$index]."',".$data[0].");\n");
					}
			}
			fclose($fh);
		}
	}
?>
