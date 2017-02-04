<?php
	session_start();
	use \php\Page\Deck;
	include_once 'autoloader.php';
	
	if(!isset($_SESSION['username']) || !isset($_GET['mazzo']) || $_GET['mazzo'] == "")
		header("Location: index.php");
	
	$obj = new Deck();
	$obj->eliminaMazzo($_GET['mazzo']);
	header("Location: user.php");
?>