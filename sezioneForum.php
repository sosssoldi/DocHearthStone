<?php
	session_start();
    include_once 'autoloader.php';
    use \php\Page\sectionForum;
	if (!isset($_GET['nome']) || $_GET['nome']=="")
		header('Location:forum.php');
	echo file_get_contents("html/sezioneForum_head.html");
    $obj= new sectionForum();
    $obj->header();
    $obj->content();
    $obj->footer();
	echo file_get_contents("html/chiudi.html");
?>
