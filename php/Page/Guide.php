<?php
namespace php\Page;

include_once "Page.php";

use \php\Database\Database;
use \php\Database\MySQLConnection\MySQLConnection;

class Guide implements Page {
	
	private $db;
	
	public function __construct() {
		$this->db = new Database(new MySQLConnection());
	}
	
	public function header() {
		$head = file_get_contents("html/header.html");

		//controllo utente loggato
		if(isset($_SESSION["username"])) {
			$head = str_replace(":login:",
			'<li><a href="user.php">'.$_SESSION["username"].'</a></li>', $head);
			$head = str_replace(":utente:",
			'<div id="boxutente">
				<span>'.$_SESSION["username"].'</span>
				<a href="logout.php"><button>Logout</button></a>
			</div>'
			,$head);
		}
		else {
			$head = str_replace(":login:",
			'<li lang="en"><a href="login.php">LOGIN</a></li>', $head);
			$head = str_replace(":utente:",'',$head);
		}

		//here
		$head = str_replace('<li><a href="guide.php">GUIDE</a></li>',
		'<li class="here">GUIDE</li>', $head);

		echo $head;
	}
	
	public function headerHero() {
		$head = file_get_contents("html/header.html");

		//controllo utente loggato
		if(isset($_SESSION["username"])) {
			$head = str_replace(":login:",
			'<li><a href="user.php">'.$_SESSION["username"].'</a></li>', $head);
			$head = str_replace(":utente:",
			'<div id="boxutente">
				<span>'.$_SESSION["username"].'</span>
				<a href="logout.php"><button>Logout</button></a>
			</div>'
			,$head);
		}
		else {
			$head = str_replace(":login:",
			'<li lang="en"><a href="login.php">LOGIN</a></li>', $head);
			$head = str_replace(":utente:",'',$head);
		}

		echo $head;
	}
	
	public function content() {
		echo file_get_contents("html/guide.html");
	}
	
	public function footer() {
		echo file_get_contents("html/footer.html");
	}
	
	public function contentHero() {
		$contenuto = file_get_contents("html/guideEroe.html");
		
		if($_GET['eroe'] != 'Generale')
			$contenuto = str_replace(':nomeGuide:', 'Le guide di Doc Hearthstone per il '.$_GET['eroe'], $contenuto);
		else
			$contenuto = str_replace(':nomeGuide:', 'Le guide generali di Doc Hearthstone', $contenuto);
		
		//creo la tabella
		if($_GET['eroe'] == 'Generale')
			$query = 'SELECT guide_id, title, user_name, valutation FROM guide WHERE hero_id is null';
		else
			$query = 'SELECT guide_id, title, user_name, valutation FROM guide, hero WHERE guide.hero_id = hero.hero_id AND hero.type = "'.$_GET['eroe'].'"';

		$this->db->query($query);
		$rs = $this->db->resultset();
		
		$risultato = '';
		
		foreach($rs as $row) {
			$risultato .= '<tr>';
			$risultato .= '<td><a href="mostraGuida.php?mazzo='.$row['guide_id'].'">'.$row['title'].'</a></td>';
			$risultato .= '<td class="username">'.$row['user_name'].'</td>';
			$risultato .= '<td class="valutazione">'.$row['valutation'].'</td>';
			$risultato .= '</tr>';
		}
		
		$contenuto = str_replace(':bodyTabella:', $risultato, $contenuto);
		
		echo $contenuto;
	}
}
?>
