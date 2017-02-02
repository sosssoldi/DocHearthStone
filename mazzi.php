<?php
	session_start();
	include_once 'autoloader.php';
	use \php\Page\Deck;
	echo file_get_contents("html/mazzi_head.html");
	$obj= new Deck();
	$obj->header();
	$obj->content();
	$obj->footer();
	echo file_get_contents("html/chiudi.html");
?>
