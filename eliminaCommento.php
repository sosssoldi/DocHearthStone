<?php
	session_start();
    use \php\Page\Discussion;
    include_once 'autoloader.php';

    if(!isset($_SESSION['username']) || !isset($_GET['commento']) || $_GET['commento'] == "" || $_SESSION['username'] != 'admin')
        header("Location: index.php");

    $obj = new Discussion();
    $obj->eliminaCommento($_GET['commento']);
    header("Location: forum.php");
?>