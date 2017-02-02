<?php
	session_start();
    include_once 'autoloader.php';
    use \php\Page\User;

	if(!isset($_SESSION["username"]))
		header("Location: login.php");

	echo file_get_contents("html/utente_head.html");
    $obj = new User();
    $obj->header();
    $obj->content();
    $obj->footer();
	echo file_get_contents("html/chiudi.html");
?>
