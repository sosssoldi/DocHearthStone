<?php
	session_start();
    include_once 'autoloader.php';
    use \php\Page\Guide;
	echo file_get_contents("html/guideEroe_head.html");
    $obj= new Guide();
    $obj->header();
    $obj->contentRicerca();
    $obj->footer();
	echo file_get_contents("html/chiudi.html");
?>
