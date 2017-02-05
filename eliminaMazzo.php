<?php
	session_start();
	use \php\Page\Deck;
	include_once 'autoloader.php';

	/*
	status = 1 -> utente loggato e admin
	status = 2 -> utente loggato
	*/

	//controllo se admin e se provengo da una delle 2 pagine in questione
	if(isset($_GET['mazzo']) && isset($_SESSION['username']) && $_SESSION['username'] == 'admin')
		$status=1;
	//controllo se utente loggato e provengo da form valido
	else if(isset($_SESSION['username']) && isset($_GET['mazzo']) && $_GET['mazzo'] != "")
		$status=2;
	else
		header("Location: index.php");

	$obj = new Deck();
	$obj->eliminaMazzo($_GET['mazzo'],$status);
	
	if($status==1)
		header("Location: mazzi.php");
	else
		header("Location: user.php");
?>
