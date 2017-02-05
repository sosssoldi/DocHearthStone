<?php
	session_start();
	use \php\Page\Guide;
	include_once 'autoloader.php';

	/*
	status = 1 -> utente loggato e admin
	status = 2 -> utente loggato
	*/

	//controllo se admin e se provengo da una delle 2 pagine in questione
	if(isset($_GET['guida']) && isset($_SESSION['username']) && $_SESSION['username'] == 'admin')
		$status=1;
	
	//controllo se utente loggato e provengo da form valido
	else if(isset($_SESSION['username']) && isset($_GET['guida']) && $_GET['guida'] != "")
		$status=2;
	else
		header("Location: index.php");

	$obj = new Guide();
	$obj->eliminaGuida($_GET['guida'],$status);
	
	if($status==1)
		header("Location: guide.php");
	else
		header("Location: user.php");
?>
