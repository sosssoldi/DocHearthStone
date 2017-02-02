<?php
    session_start();
    include_once 'autoloader.php';
    use \php\Page\Registrazione;
	
	if(isset($_SESSION['username']))
		header("Location: index.php");
    
	echo file_get_contents("html/registrazione_head.html");
    $obj = new Registrazione();
    $obj->header();
    $obj->content();
    $obj->footer();
    echo file_get_contents("html/chiudi.html");
?>
