<?php
	session_start();
	include_once 'autoloader.php';
	use \php\Page\createDeck;
	echo file_get_contents("html/creaMazzo_head.html");
	$obj= new createDeck();
	$obj->header();
	$obj->content();
	$obj->footer();
	echo file_get_contents("html/chiudi.html");
?>