<?php
	session_start();
	include_once 'autoloader.php';
	use \php\Page\Card;
	echo file_get_contents("html/carte_head.html");
	$obj = new Card();
	$obj->header();
	$obj->content();
	$obj->footer();
	echo file_get_contents("html/chiudi.html");
?>
