<?php

namespace php\Page;

include_once "Page.php";

use \php\Database\Database;
use \php\Database\MySQLConnection\MySQLConnection;

class Card implements Page {

	private $db = null;

	public function __construct() {
		$this->db = new Database(new MySQLConnection());
	}
	
	public function header() {
		
		$head = file_get_contents("html/header.html");
	
		//controllo utente loggato
        if(isset($_SESSION["username"])) {
            $head = str_replace(':login:',
			'<li><a href="user.php">'.$_SESSION["username"].'</a></li>', $head);
        }
        else {
            $head = str_replace(':login:',
			'<li lang="en"><a href="login.php">LOGIN</a></li>', $head);
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
		
		//nome
		if(isset($_GET['nome'])) {
			$contenuto = str_replace(':nomeCarta:',
			'<input type="text" name="nome" value="'.$_GET['nome'].'"/>', $contenuto);
		}
		else {
			$contenuto = str_replace(':nomeCarta:',
			'<input type="text" name="nome"/>', $contenuto);
		}
		
		//costo
		if(isset($_GET['costo'])) {
			$contenuto = str_replace('<option>'.$_GET['costo'].'</option>',
			'<option selected>'.$_GET['costo'].'</option>', $contenuto);
		}

		//avventura ed espansione
		if(isset($_GET['avventura'])) {
			$contenuto = str_replace('<option>'.$_GET['avventura'].'</option>',
			'<option selected>'.$_GET['avventura'].'</option>', $contenuto);
		}
		
		//rarita
		if(isset($_GET['rarita'])) {
			$contenuto = str_replace('<option>'.$_GET['rarita'].'</option>',
			'<option selected>'.$_GET['rarita'].'</option>', $contenuto);
		}
		
		//tipo
		if(isset($_GET['tipo'])) {
			$contenuto = str_replace('<option>'.$_GET['tipo'].'</option>',
			'<option selected>'.$_GET['tipo'].'</option>', $contenuto);
		}
		
		//classe
		if(isset($_GET['classe'])) {
			$contenuto = str_replace('<option>'.$_GET['classe'].'</option>',
			'<option selected>'.$_GET['classe'].'</option>', $contenuto);
		}
		
		//prendo tutte le carte
		$query = $this->generaQuery();
		$this->db->query($query);
		$rs = $this->db->resultset();
		
		$riga = '';
		
		foreach($rs as $row) {
			$riga .= '<tr>';
			$riga .= '<td><span class="'.$row['rarity'].'">'.$row['name'].'</span><span class="descrizione">'.$row['description'].'</span></td>';
			$riga .= '<td>'.$row['c_type'].'</td>';
			
			if($row['numClassi'] == 9)
				$riga .= '<td>Neutrale</td>';
			else
				$riga .= '<td>'.$this->stampaClassi($row['name']).'</td>';
			
			$riga .= '<td class="mana">'.$row['mana'].'</td>';
			$riga .= '<td class="attacco">'.$row['attack'].'</td>';
			$riga .= '<td class="vita">'.$row['health'].'</td>';
			$riga .= '</tr>';
		}
		
		$contenuto = str_replace(':corpoTabella:', $riga, $contenuto);
		
		//content del documento
		echo $contenuto;
	}
	
	public function footer() {
		echo file_get_contents("html/footer.html");
	}
	
	private function generaQuery() {
		
		$query = 'SELECT card.name, rarity, c_type, mana, attack, health, description, COUNT(card.name) as numClassi FROM hero, card, hero_card WHERE hero.hero_id = hero_card.hero_id AND card.card_id = hero_card.card_id';
		
		//nome carta
		if(isset($_GET['nome']) AND $_GET['nome'] != '')
			$query .= ' AND name = "'.$_GET['nome'].'"';

		//costo in mana della carta
		if(isset($_GET['costo']) AND $_GET['costo'] != '')
			if($_GET['costo'] == '7+')
				$query .= ' AND mana >= 7';
			else
				$query .= ' AND mana = '.$_GET['costo'];
		
		//avventura/espansione
		if(isset($_GET['avventura']) AND $_GET['avventura'] != '') {
			
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

			$query .= ' AND (adventure_name = "'.$avventura.'" OR expansion_name = "'.$avventura.'")';
		}
		
		//rarita
		if(isset($_GET['rarita']) AND $_GET['rarita'] != '')
			$query .= ' AND rarity = "'.$_GET['rarita'].'"';
		
		//tipo
		if(isset($_GET['tipo']) AND $_GET['tipo'] != '')
			$query .= ' AND c_type = "'.$_GET['tipo'].'"';
		
		//classe
		if(isset($_GET['classe']) AND $_GET['classe'] != '')
			$query .= ' AND type = "'.$_GET['classe'].'"';
		
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
}
?>
