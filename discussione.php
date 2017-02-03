<?php
	session_start();
	include_once 'autoloader.php';
	use \php\Page\Discussion;
    if (!isset($_GET['id']) || $_GET['id']=="")
        header('Location:forum.php');
	echo file_get_contents("html/discussione_head.html");
	$obj= new Discussion();
	$obj->header();
	$obj->content();
	$obj->footer();
	echo file_get_contents("html/chiudi.html");
?>
