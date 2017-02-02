<?php
	session_start();
	include_once 'autoloader.php';
	use \php\Page\Guide;
	
	if(!isset($_GET['eroe']) || ($_GET['eroe'] != 'Generale' && $_GET['eroe'] != 'Mago' && $_GET['eroe'] != 'Cacciatore' && $_GET['eroe'] != 'Ladro' && $_GET['eroe'] != 'Guerriero' && $_GET['eroe'] != 'Sciamano' && $_GET['eroe'] != 'Druido' && $_GET['eroe'] != 'Sacerdote' && $_GET['eroe'] != 'Stregone' && $_GET['eroe'] != 'Paladino'))
		header("Location: guide.php");
	
	echo file_get_contents("html/guideEroe_head.html");
	$obj= new Guide();
	$obj->headerHero();
	$obj->contentHero();
	$obj->footer();
	echo file_get_contents("html/chiudi.html");
?>