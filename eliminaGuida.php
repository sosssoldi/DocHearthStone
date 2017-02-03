<?php
	session_start();
	use \php\Page\Guide;
	include_once 'autoloader.php';
	
	if(!isset($_SESSION['username']) || !isset($_GET['guida']) || $_GET['guida'] == "")
		header("Location: index.php");
	
	$obj = new Guide();
	$obj->eliminaGuida($_GET['guida']);
	header("Location: user.php");
?>