<?php
	session_start();
    include_once 'autoloader.php';
    use \php\Page\aggGuida;

	if(!isset($_SESSION["username"]))
		header("Location: login.php");

    echo file_get_contents("html/aggGuida_head.html");
    $obj= new AggGuida();
    $obj->header();
    $obj->content();
    $obj->footer();
	echo file_get_contents("html/chiudi.html");
?>
