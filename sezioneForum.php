<?php
	session_start();
    include_once 'autoloader.php';
    use \php\Page\sectionForum;
	echo file_get_contents("html/sezioneForum_head.html");
    $obj= new sectionForum();
    $obj->header();
    $obj->content();
    $obj->footer();
	echo file_get_contents("html/chiudi.html");
?>
