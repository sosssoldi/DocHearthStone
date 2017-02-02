<?php
	session_start();
	include_once 'autoloader.php';
	use \php\Page\Guide;
	
	if(!isset($_GET['guida']) || $_GET['guida'] == "")
		header("Location: guide.php");
	
	echo file_get_contents("html/guideEroe_head.html");
	$obj = new Guide();
	$obj->headerHero();
	$obj->contentGuida();
	$obj->footer();
	echo file_get_contents("html/chiudi.html");
?>