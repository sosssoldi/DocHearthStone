<?php
	include_once 'autoloader.php';
	use \php\Page\Guide;
	echo file_get_contents("html/guide_head.html");
	$obj = new Guide();
	$obj->header();
	$obj->content();
	$obj->footer();
	echo file_get_contents("html/chiudi.html");
?>
