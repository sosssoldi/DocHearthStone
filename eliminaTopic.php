<?php
    session_start();
    use \php\Page\sectionForum;
    include_once 'autoloader.php';

    if(!isset($_SESSION['username']) || !isset($_GET['topic']) || $_GET['topic'] == "" || $_SESSION['username'] != 'admin')
        header("Location: index.php");

    $obj = new sectionForum();
    $obj->eliminaTopic($_GET['topic']);
    header("Location: forum.php");



 ?>
