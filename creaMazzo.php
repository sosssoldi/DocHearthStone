<?php
	session_start();
	include_once 'autoloader.php';
	use \php\Page\createDeck;
	if (!isset($_GET['eroe']) || $_GET['eroe']=="")
		header('Location:scegliClasse.php');
	echo file_get_contents("html/creaMazzo_head.html");
	$obj= new createDeck();
	$obj->header();
	$obj->content();
	$obj->footer();
	echo file_get_contents("html/chiudi.html");
?>
