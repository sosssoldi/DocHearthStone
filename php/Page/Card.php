<?php

namespace php\Page;

include_once "Page.php";

use \php\Database\Database;
use \php\Database\MySQLConnection\MySQLConnection;

class Card implements Page {

	private $db;

	public function __construct() {
		$db = new Database(new MySQLConnection());
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
		
		//content del documento
		echo $contenuto;
	}
	
	public function footer() {
		
	}
}
?>
