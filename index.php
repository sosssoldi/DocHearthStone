<?php
	session_start();
    include_once 'autoloader.php';
    use \php\Page\Homepage;

	echo file_get_contents("html/index_head.html");
    $obj = new Homepage();
    $obj->header();
    $obj->content();
    $obj->footer();
	echo file_get_contents("html/chiudi.html");
?>
