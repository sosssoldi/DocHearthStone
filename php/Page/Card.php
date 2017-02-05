<?php

namespace php\Page;

include_once "Page.php";

use \php\Database\Database;
use \php\Database\MySQLConnection\MySQLConnection;

class Card implements Page {

	private $db = null;
	private $errore = array(
						"",
						"Errore: I dati inseriti non sono corretti",
	);

	public function __construct() {
		$this->db = new Database(new MySQLConnection());
	}

	public function header() {

		$head = file_get_contents("html/header.html");

		//controllo utente loggato
        if(isset($_SESSION["username"])) {
            $head = str_replace(':login:',
			'<li><a href="user.php">'.$_SESSION["username"].'</a></li>', $head);
			$head = str_replace(":utente:",
				'<form id="logout" action="logout.php" method="get">
					<span>'.$_SESSION["username"].'</span>
					<input id="logoutButton" type="submit" value="Logout">
				</form>'
			,$head);
        }
        else {
            $head = str_replace(':login:',
			'<li lang="en"><a href="login.php">LOGIN</a></li>', $head);
			$head = str_replace(":utente:",'',$head);
        }

		//here
        $head = str_replace('<li><a href="carte.php">CARTE</a></li>',
		'<li class="here">CARTE</li>', $head);

		//header del documento
        echo $head;
	}

	public function content() {

		$contenuto = file_get_contents("html/carte.html");

		//controllo se qualche campo di $_GET è stato settato
		$err = 0;
		//nome
		if(isset($_GET['nome'])) {
			if(strstr($_GET['nome'], '>') || strstr($_GET['nome'], '<'))
				$err = 1;

			$nome = $_GET["nome"];
			$_GET['nome'] = str_replace("<", "&lt;", $_GET["nome"]);
			$_GET['nome'] = str_replace(">", "&gt;", $_GET["nome"]);
			$_GET['nome'] = str_replace("'", "\'", $_GET["nome"]);
			$contenuto = str_replace(':nomeCarta:',
			'<input type="text" name="nome" value="'.$nome.'"/>', $contenuto);
		}
		else {
			$contenuto = str_replace(':nomeCarta:',
			'<input type="text" name="nome"/>', $contenuto);
		}

		//costo
		$cost = array("Qualsiasi costo","1","2","3","4","5","6","7+");
		$found = 0;
		if(isset($_GET['costo'])) {
			foreach ($cost as $c)
				if($c == $_GET["costo"])
					$found = 1;
			if($found == 0)
				$err = 1;
			$contenuto = str_replace('<option>'.$_GET['costo'].'</option>',
			'<option selected>'.$_GET['costo'].'</option>', $contenuto);
		}

		//avventura ed espansione
		$adventures = array(
				"Tutte le espansioni",
				"Set Base",
				"Classico",
				"Naxxramas",
				"Massiccio Roccianera",
				"Lega degli Esploratori",
				"Karazhan",
				"Goblin vs Golem",
				"Sussurro degli Dei Antichi",
				"I bassifondi di Meccania",
				"Il Gran Torneo"
		);
		$found = 0;
		if(isset($_GET['avventura'])) {
			foreach ($adventures as $adv)
				if($adv == $_GET["avventura"])
					$found = 1;
			if($found == 0)
				$err = 1;
			$contenuto = str_replace('<option>'.$_GET['avventura'].'</option>',
			'<option selected>'.$_GET['avventura'].'</option>', $contenuto);
		}

		//rarita
		$rarity = array(
				"Tutte le rarità",
				"Comune",
				"Rara",
				"Epica",
				"Leggendaria"
		);
		$found = 0;
		if(isset($_GET['rarita'])) {
			foreach ($rarity as $r)
				if($r == $_GET["rarita"])
					$found = 1;
			if($found == 0)
				$err = 1;
			$contenuto = str_replace('<option>'.$_GET['rarita'].'</option>',
			'<option selected>'.$_GET['rarita'].'</option>', $contenuto);
		}

		//tipo
		$type = array(
				"Tutti i tipi",
				"Servitore",
				"Magia",
				"Arma"
		);
		$found = 0;
		if(isset($_GET['tipo'])) {
			foreach ($type as $t)
				if($t == $_GET["tipo"])
					$found = 1;
			if($found == 0)
				$err = 1;
			$contenuto = str_replace('<option>'.$_GET['tipo'].'</option>',
			'<option selected>'.$_GET['tipo'].'</option>', $contenuto);
		}

		//classe
		$heroes = array("Tutte le classi",
				"Mago",
				"Sacerdote",
				"Druido",
				"Cacciatore",
				"Guerriero",
				"Paladino",
				"Stregone",
				"Sciamano",
				"Ladro"
		);
		$found = 0;
		if(isset($_GET['classe'])) {
			foreach ($heroes as $h)
				if($h == $_GET["classe"])
					$found = 1;
			if($found == 0)
				$err = 1;
			$contenuto = str_replace('<option>'.$_GET['classe'].'</option>',
			'<option selected>'.$_GET['classe'].'</option>', $contenuto);
		}

		$contenuto = str_replace(":errore:","<p class=\"errore\">".$this->errore[$err]."</p>",$contenuto);
		if($err == 0) {
			//prendo tutte le carte
			$query = $this->generaQuery();
			$this->db->query($query);
			$rs = $this->db->resultset();

			$riga = '';

			foreach($rs as $row) {
				$riga .= '<tr>';
				$riga .= '<td class="nomeCarta"><span class="'.$row['rarity'].'" onmouseover="showImg(this, \''.$row["id"].'\');" onmouseout="hideImg(this);">'.$row['name'].'</span><span class="descrizione">'.$row['description'].'</span></td>';
				$riga .= '<td class="tipoCarta">'.$row['c_type'].'</td>';

				if($row['numClassi'] == 9)
					$riga .= '<td class="classeCarta">Neutrale</td>';
				else
					$riga .= '<td class="classeCarta">'.$this->stampaClassi($row['name']).'</td>';

				$avventura = $row['expansion_name'];

				if($avventura == 'EXPERT1')
					$avventura = 'Classico';

				if($avventura == 'CORE')
					$avventura = "Set Base";

				$riga .= '<td class="espansioneCarta">'.$avventura.'</td>';
				$riga .= '<td class="mana">'.$row['mana'].'</td>';
				$riga .= '<td class="attacco">'.$row['attack'].'</td>';
				$riga .= '<td class="vita">'.$row['health'].'</td>';
				$riga .= '</tr>';
			}
			$contenuto = str_replace(':corpoTabella:', $riga, $contenuto);
		}
		else {
			$contenuto = str_replace(':corpoTabella:', "", $contenuto);
		}

		//content del documento
		echo $contenuto;
	}

	public function footer() {
		echo file_get_contents("html/footer.html");
	}

	private function generaQuery() {

		$query = 'SELECT card.card_id as id, card.name, rarity, c_type, mana, attack, health, description, expansion_name, COUNT(card.name) as numClassi FROM hero, card, hero_card WHERE hero.hero_id = hero_card.hero_id AND card.card_id = hero_card.card_id';

		//nome carta
		if(isset($_GET['nome']) AND $_GET['nome'] != '')
			$query .= ' AND card.name LIKE "%'.$_GET['nome'].'%"';

		//costo in mana della carta
		$this->checkCosto();
		if(isset($_GET['costo']) && $_GET['costo'] != "Qualsiasi costo")
				if($_GET['costo'] == '7+')
					$query .= ' AND mana >= 7';
				else
					$query .= ' AND mana = '.$_GET['costo'];
				
		//avventura/espansione
		if(isset($_GET['avventura']) AND $_GET['avventura'] != 'Tutte le espansioni') {
			$avventura='';
			if($_GET['avventura'] == "Set Base")
				$avventura = 'CORE';

			if($_GET['avventura'] == "Classico")
				$avventura = 'EXPERT1';

			if($_GET['avventura'] == "Karazhan")
				$avventura='KARA';

			if($_GET['avventura'] == "Massiccio Roccianera")
				$avventura='BRM';

			if($_GET['avventura'] == "Lega degli Esploratori")
				$avventura='LOE';

			if($_GET['avventura'] == "Naxxramas")
				$avventura='NAXX';

			if($_GET['avventura'] == "Goblin vs Golem")
				$avventura='GVG';

			if($_GET['avventura'] == "Sussurro degli Dei Antichi")
				$avventura='OG';

			if($_GET['avventura'] == "I bassifondi di Meccania")
				$avventura='GANGS';

			if($_GET['avventura'] == "Il Gran Torneo")
				$avventura='TGT';

			if ($avventura!='')
				$query .= ' AND expansion_name = "'.$avventura.'"';
		}

		//rarita
		if(isset($_GET['rarita']) AND $_GET['rarita'] != 'Tutte le rarità')
		{
			$rarita=$_GET['rarita'];
			if ($rarita=="Comune" || $rarita=="Rara" || $rarita=="Epica" || $rarita=="Leggendaria")
				$query .= ' AND rarity = "'.$rarita.'"';
		}

		//tipo
		if(isset($_GET['tipo']) AND $_GET['tipo'] != 'Tutti i tipi')
		{
			$tipo=$_GET['tipo'];
			if ($tipo=="Servitore"|| $tipo=="Arma" || $tipo=="Magia")
				$query .= ' AND c_type = "'.$tipo.'"';
		}

		//classe
		if(isset($_GET['classe']) && $_GET['classe'] != 'Tutte le classi')
		{
			$this->getID();
			$query .= ' AND type = "'.$_GET['classe'].'"';
		}

		$query .= ' GROUP BY card.name, c_type, mana, attack, health;';

		return $query;
	}

	private function stampaClassi($nomeCarta) {

		$elencoClassi = '';

		$query = 'SELECT type FROM hero, card, hero_card WHERE hero.hero_id = hero_card.hero_id AND card.card_id = hero_card.card_id AND card.name = "'.$nomeCarta.'"';

		$this->db->query($query);
		$rs = $this->db->resultset();

		if($this->db->rowCount($rs) == 9)
			$elencoClassi .= 'Neutrale';
		else {
			foreach($rs as $row) {
				if($elencoClassi == '')
					$elencoClassi .= $row['type'];
				else
					$elencoClassi .= ', '.$row['type'];
			}
		}

		return $elencoClassi;
	}

	//controlli per query string inventate
	public function checkCosto() {
		if (isset($_GET['costo']) && ($_GET['costo']<0 || $_GET['costo']>8))
			$_GET['costo']="";
	}

	public function getID() {

		$query='SELECT H.hero_id
				FROM hero H
				WHERE H.type="'.$_GET['classe'].'"';

		$this->db->query($query);
		$rs = $this->db->resultset();

		if($this->db->rowCount()==0)
			$_GET['classe']='';
	}
}
?>
