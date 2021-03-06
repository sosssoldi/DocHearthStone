<?php
namespace php\Page;

include_once "Page.php";

use \php\Database\Database;
use \php\Database\MySQLConnection\MySQLConnection;

class AddDeck implements Page {

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
				<input id="logoutButton" type="submit" value="Logout" />
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
		echo file_get_contents("html/scegliclasse.html");
	}

	public function footer() {
		echo file_get_contents("html/footer.html");
	}
}
?>
