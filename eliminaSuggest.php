<?php
    session_start();
    use \php\Page\showDeck;
    include_once 'autoloader.php';

    if(!isset($_GET['id_mazzo']) || !isset($_SESSION['username']) || !isset($_GET['suggest']) || $_GET['id_mazzo'] == "" || $_GET['suggest'] == "" || $_SESSION['username'] != 'admin')
        header("Location: index.php");

    $obj = new showDeck();
    $obj->eliminaSuggest($_GET['suggest']);
    header("Location: mostraMazzo.php?mazzo=".$_GET['id_mazzo']);
 ?>
