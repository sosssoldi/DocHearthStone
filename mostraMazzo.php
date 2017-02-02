<?php
	session_start();
    include_once 'autoloader.php';
    use \php\Page\showDeck;
	if (!isset($_GET['mazzo']) || $_GET['mazzo']=="")
		header('Location:mazzi.php');
	echo file_get_contents('html/mostraMazzo_head.html');
    $obj=new showDeck();
    $obj->header();
    $obj->content();
    $obj->footer();
	echo file_get_contents("html/chiudi.html");
?>
