<?php
	session_start();
	include_once 'autoloader.php';
	use \php\Page\Adventure;
	echo file_get_contents("html/avventure_head.html");
	$obj= new Adventure();
	$obj->header();
	$obj->content();
	$obj->footer();
	echo file_get_contents("html/chiudi.html");
?>
