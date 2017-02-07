<?php
	session_start();
    include_once 'autoloader.php';
    use \php\Page\Sitemap;
	echo file_get_contents("html/sitemap_head.html");
    $obj = new Sitemap();
    $obj->header();
    $obj->content();
    $obj->footer();
	echo file_get_contents("html/chiudi.html");
?>
