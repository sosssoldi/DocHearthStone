<?php
	session_start();
	include_once 'autoloader.php';
	use \php\Page\Guide;
	
	if(!isset($_GET['eroe']) || ($_GET['eroe'] != 'generale' && $_GET['eroe'] != 'mago' && $_GET['eroe'] != 'cacciatore' && $_GET['eroe'] != 'ladro' && $_GET['eroe'] != 'guerriero' && $_GET['eroe'] != 'sciamano' && $_GET['eroe'] != 'druido' && $_GET['eroe'] != 'sacerdote' && $_GET['eroe'] != 'stregone' && $_GET['eroe'] != 'paladino'))
		header("Location: guide.php");
	
	echo file_get_contents("html/guide_head.html");
	echo file_get_contents("html/chiudi.html");
?>