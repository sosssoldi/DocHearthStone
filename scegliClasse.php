<?php
	session_start();
	include_once 'autoloader.php';
	use \php\Page\AddDeck;
    if(!isset($_SESSION["username"]))
        header("Location: login.php");
	echo file_get_contents("html/scegliclasse_head.html");
	$obj = new AddDeck();
	$obj->header();
	$obj->content();
	$obj->footer();
	echo file_get_contents("html/chiudi.html");
?>
