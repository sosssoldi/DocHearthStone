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
					'<form id="logout" action="logout.php" method="get">
	                    <span>'.$_SESSION["username"].'</span>
	                    <input id="logoutButton" type="submit" value="Logout">
	                </form>'
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
					'<form id="logout" action="logout.php" method="get">
	                    <span>'.$_SESSION["username"].'</span>
	                    <input id="logoutButton" type="submit" value="Logout">
	                </form>'
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
		$content=file_get_contents("html/guide.html");
		echo $content;
	}

	public function footer() {
		echo file_get_contents("html/footer.html");
	}

	//stampa un content differente se c'è stata una ricerca
	public function contentRicerca() {
		$contenuto = file_get_contents("html/guideEroe.html");

		$contenuto = str_replace(':nomeGuide:', 'La tua ricerca ha prodotto i seguenti risultati:', $contenuto);

		if($this->admin())
			$contenuto=str_replace(':admin:','<th class="username">Elimina</th>',$contenuto);
		else
			$contenuto=str_replace(':admin:','',$contenuto);

		if($_GET["barra"]=="")
			header("Location: guide.php");

		$pezzi = explode(" ", $_GET['barra']);
		foreach ($pezzi as &$p) {
			$p = htmlspecialchars($p);
			$p = str_replace("'","\'",$p);
		}
		$resultArray = array();

		//la ricerca viene fatta sia su contenuto che su title
		$query = 'SELECT guide_id, title, user_name
				FROM guide
				WHERE content LIKE "%'.$pezzi[0].'%" OR title LIKE "%'.$pezzi[0].'%"';

		for($i=1;$i<count($pezzi);$i++)
			$query .= ' OR T.content LIKE "%'.$pezzi[$i].'%" OR title LIKE "%'.$pezzi[$i].'%"';

		$this->db->query($query);
		$rs = $this->db->resultset();

		$resultArray = array_merge($resultArray, $rs);

		$risultato = '';

		foreach($resultArray as $row) {
			$risultato .= '<tr>';
			$risultato .= '<td><a href="mostraGuida.php?guida='.$row['guide_id'].'">'.$row['title'].'</a></td>';
			$risultato .= '<td class="username">'.$row['user_name'].'</td>';
			if($this->admin())
				$risultato .= '<td class="username"><a href="eliminaGuida.php?guida='.$row['guide_id'].'">
				<img class="delete" src="images/icon/remove.png" alt="Elimina guida"></a></td>';
			$risultato .= '</tr>';

		}
		$contenuto = str_replace(':bodyTabella:', $risultato, $contenuto);

		echo $contenuto;
	}

	//stampa tutte le guide di un determinato hero
	public function contentHero() {
		$contenuto = file_get_contents("html/guideEroe.html");

		if($this->admin())
			$contenuto=str_replace(':admin:','<th class="username">Elimina</th>',$contenuto);
		else
			$contenuto=str_replace(':admin:','',$contenuto);


		if($_GET['eroe'] != 'Generale')
			$contenuto = str_replace(':nomeGuide:', 'Le guide di Doc Hearthstone per il '.$_GET['eroe'], $contenuto);
		else
			$contenuto = str_replace(':nomeGuide:', 'Le guide generali di Doc Hearthstone', $contenuto);

		//cerco le guide in base all'eroe selezionato (se generale allora l'eroe non c'è e quindi NULL)
		if($_GET['eroe'] == 'Generale')
			$query = 'SELECT guide_id, title, user_name FROM guide WHERE hero_id is null';
		else
			$query = 'SELECT guide_id, title, user_name FROM guide, hero WHERE guide.hero_id = hero.hero_id AND hero.type = "'.$_GET['eroe'].'"';

		$this->db->query($query);
		$rs = $this->db->resultset();

		$risultato = '';

		foreach($rs as $row) {
			$risultato .= '<tr>';
			$risultato .= '<td><a href="mostraGuida.php?guida='.$row['guide_id'].'">'.$row['title'].'</a></td>';
			$risultato .= '<td class="username">'.$row['user_name'].'</td>';
			if($this->admin())
				$risultato .= '<td class="username"><a href="eliminaGuida.php?guida='.$row['guide_id'].'">
				<img class="delete" src="images/icon/remove.png" alt="Elimina guida"></a></td>';
			$risultato .= '</tr>';
		}

		$contenuto = str_replace(':bodyTabella:', $risultato, $contenuto);

		echo $contenuto;
	}

	public function contentGuida() {

		$contenuto = file_get_contents("html/mostraGuida.html");

		$query = 'SELECT title, content, hero_id FROM guide WHERE guide_id = '.$_GET['guida'];
		$this->db->query($query);
		$rs = $this->db->resultset();

		if($this->db->rowCount() > 0) {
			$contenuto = str_replace(':titoloGuida:', $rs[0]['title'], $contenuto);
			
			if($rs[0]['hero_id'] != NULL) {
				$contenuto = str_replace(':sezioneGuida:', 'Le guide di Doc Hearthstone per il :nomeEroe:', $contenuto);
				$query = 'SELECT type FROM hero WHERE hero_id = "'.$rs[0]['hero_id'].'"';
				$this->db->query($query);
				$typeHero = $this->db->resultset();
				$contenuto = str_replace(':nomeEroe:', $typeHero[0]['type'], $contenuto);
			}
			else {
				$contenuto = str_replace(':sezioneGuida:', 'Le guide generali di Doc Hearthstone', $contenuto);
				$contenuto = str_replace(':nomeEroe:', 'Generale', $contenuto);
			}
			
			if($rs[0]['content'] != "NULL")
				$contenuto = str_replace(':contenutoGuida:', $rs[0]['content'], $contenuto);
			else
				$contenuto = str_replace(':contenutoGuida:', '', $contenuto);
		}
		else
			header("Location: guide.php");

		echo $contenuto;
	}

	public function eliminaGuida($id,$status) {

		$query = 'DELETE FROM guide WHERE guide_id = '.$id;
		if($status == 2)
			$query .= ' AND user_name = "'.$_SESSION['username'].'"';
		$this->db->query($query);
		$this->db->execute();
	}

	private function admin() {
		if(isset($_SESSION["username"]) && $_SESSION["username"] == 'admin')
			return true;
		else return false;
	}
}
?>
