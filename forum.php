<?php
	session_start();
    include_once 'autoloader.php';
    use \php\Page\Forum;
	echo file_get_contents("html/forum_head.html");
    $obj= new Forum();
    $obj->header();
    $obj->content();
    $obj->footer();
	echo file_get_contents("html/chiudi.html");
?>
