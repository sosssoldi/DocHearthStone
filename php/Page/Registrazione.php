<?php
namespace php\Page;

include_once "Page.php";

use \php\Database\Database;
use \php\Database\MySQLConnection\MySQLConnection;

class Registrazione implements Page {

	private $db = null;
	private $status = 0;
	/*
	status = 0 -> nessun tentativo di registrazione
	status = 1 -> errore: qualche campo è vuoto
	status = 2 -> errore: le password non coincidono
	status = 3 -> tentativo di registrazione
	status = 4 -> errore: username già in uso
	status = 5 -> errore: email già in uso
	status = 6 -> errore: uno o più campi contengono caratteri errati
	*/

	public function __construct() {
		$this->db = new Database(new MySQLConnection());
	}

	public function header() {

		//verifico se ho dati da elaborare
		$this->controlloRegistrazione();

		//se tentativo di registrazione verifico se va a buon fine o meno
		if($this->status == 3)
			$this->provaRegistrazione();

		$head = file_get_contents("html/header.html");

		//controllo utente loggato
		if(isset($_SESSION["username"])) {
			$head = str_replace(":login:",
			'<li><a href="user.php">'.$_SESSION["username"].'</a></li>', $head);
		}
		else {
			$head = str_replace(":login:",
			'<li lang="en"><a href="login.php">LOGIN</a></li>', $head);
		}

		$head = str_replace(':utente:', '', $head);

		echo $head;
	}

	public function content() {

		$content = file_get_contents("html/registrazione.html");

		$content = $this->cambiaLabel($content);

		//Aggiorno label in caso ci sia stato un tentativo di richiesta e alcuni campi sono accettabili
		switch($this->status) {
			case 1:
				$content = $this->aggiornaForm($content);
				break;
			case 2:
				$content = $this->aggiornaForm($content);
				break;
			case 4:
				$content = $this->aggiornaFormErrUsername($content);
				break;
			case 5:
				$content = $this->aggiornaFormErrEmail($content);
				break;
			case 6:
				$content = $this->aggiornaForm($content);
				break;
		}

		echo $content;
	}

	public function footer() {
		echo file_get_contents("html/footer.html");
	}

	//determino lo status
	private function controlloRegistrazione() {
		if(isset($_POST['nome']) && isset($_POST['cognome']) && isset($_POST['user']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password2']))
			if($_POST['nome'] != "" && $_POST['cognome'] != "" && $_POST['user'] != "" && $_POST['email'] != "" && $_POST['password'] != "" && $_POST['password2'] != "") {
				if($_POST['password'] != $_POST['password2'])
					$this->status = 2;
				else
					if($this->parseInput($_POST["nome"]) || $this->parseInput($_POST["cognome"]) || $this->parseInput($_POST["user"]) || $this->parseInput($_POST["email"]) || $this->parseInput($_POST["password"]) || $this->parseInput($_POST["password2"]))
						$this->status = 6;
					else
						$this->status = 3;
			} else
				$this->status = 1;
	}

	private function parseInput($var) {
		if(strstr($var, ">")!="" || strstr($var, "<")!="" || strstr($var, "'")!="")
			return true;
		return false;
	}

	private function provaRegistrazione() {

		$pass = hash("sha256",$_POST["password"]);
		$data = date ("Y-m-d G:i");
		$query = 'INSERT INTO user VALUES("'.$_POST['email'].'","'.$_POST['nome'].'","'.$_POST['cognome'].'","'.$_POST['user'].'","'.$pass.'","'.$data.'",0,"images/utente.jpg")';

		$this->db->query($query);
		$rs = $this->db->execute();

		//Registrazione avvenuta con successo
		if($rs == 1) {
			$_SESSION['username'] = $_POST['user'];
			$_SESSION['name'] = $_POST['nome'];
			$_SESSION['surname'] = $_POST['cognome'];
			$_SESSION['entry_date'] = $data;
			$_SESSION['photo_id'] = 'images/utente.jpg';
			header("Location: index.php");
		}
		else {
			//controllo se è già in uso la mail o l'username
			$query = 'SELECT username FROM user WHERE username = "'.$_POST['user'].'"';
			$this->db->query($query);
			$rs = $this->db->resultset();
			if($this->db->rowCount() == 1)
				$this->status = 4;
			else
				$this->status = 5;
		}
	}

	//segnalo eventuali errori sull'input dell'utente
	private function cambiaLabel($c) {

		switch($this->status) {
			case 0:
				$c = str_replace(':segnalaErrore:','',$c);
				break;
			case 1:
				$c = str_replace(':segnalaErrore:','<p>Riempire tutti i campi</p>',$c);
				break;
			case 2:
				$c = str_replace(':segnalaErrore:','<p>Le password non coincidono</p>',$c);
				break;
			case 4:
				$c = str_replace(':segnalaErrore:','<p>Username già in uso</p>',$c);
				break;
			case 5:
				$c = str_replace(':segnalaErrore:','<p>Email già in uso</p>',$c);
				break;
			case 6:
				$c = str_replace(':segnalaErrore:','<p>Nei campi non puoi inserire apostrofi e segni di minore e maggiore</p>',$c);
		}

		return $c;
	}

	//se la richiesta dell'utente genera errori, vengono riproposti i dati inseriti precedentemente
	private function aggiornaForm($c) {
		$c = str_replace('<input id="inputNome" type="text" required="required" autocomplete="off" name="nome" />','<input id="inputNome" type="text" required="required" autocomplete="off" name="nome" value="'.$_POST['nome'].'" />',$c);
		$c = str_replace('<input id="inputCognome" type="text" required="required" autocomplete="off" name="cognome" />','<input id="inputCognome" type="text" required="required" autocomplete="off" name="cognome" value="'.$_POST['cognome'].'" />',$c);
		$c = str_replace('<input id="inputUser" type="text" required="required" autocomplete="off" name="user" />','<input id="inputUser" type="text" required="required" autocomplete="off" name="user" value="'.$_POST['user'].'" />',$c);
		$c = str_replace('<input id="inputEmail" type="email" required="required" autocomplete="off" name="email" />','<input id="inputEmail" type="email" required="required" autocomplete="off" name="email" value="'.$_POST['email'].'" />',$c);
		return $c;
	}

	//se la richiesta dell'utente genera errori, vengono riproposti i dati inseriti precedentemente
	private function aggiornaFormErrUsername($c) {
		$c = str_replace('<input id="inputNome" type="text" required="required" autocomplete="off" name="nome" />','<input id="inputNome" type="text" required="required" autocomplete="off" name="nome" value="'.$_POST['nome'].'" />',$c);
		$c = str_replace('<input id="inputCognome" type="text" required="required" autocomplete="off" name="cognome" />','<input id="inputCognome" type="text" required="required" autocomplete="off" name="cognome" value="'.$_POST['cognome'].'" />',$c);
		$c = str_replace('<input id="inputEmail" type="email" required="required" autocomplete="off" name="email" />','<input id="inputEmail" type="email" required="required" autocomplete="off" name="email" value="'.$_POST['email'].'" />',$c);
		return $c;
	}

	//se la richiesta dell'utente genera errori, vengono riproposti i dati inseriti precedentemente
	private function aggiornaFormErrEmail($c) {
		$c = str_replace('<input id="inputNome" type="text" required="required" autocomplete="off" name="nome" />','<input id="inputNome" type="text" required="required" autocomplete="off" name="nome" value="'.$_POST['nome'].'" />',$c);
		$c = str_replace('<input id="inputCognome" type="text" required="required" autocomplete="off" name="cognome" />','<input id="inputCognome" type="text" required="required" autocomplete="off" name="cognome" value="'.$_POST['cognome'].'" />',$c);
		$c = str_replace('<input id="inputUser" type="text" required="required" autocomplete="off" name="user" />','<input id="inputUser" type="text" required="required" autocomplete="off" name="user" value="'.$_POST['user'].'" />',$c);
		return $c;
	}
}
?>
