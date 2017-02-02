<?php
    session_start();
    include_once 'autoloader.php';
    use \php\Page\showDeck;

    if (isset($_SESSION['username']) && isset($_GET['mazzo']) && isset($_GET['like']))
    {
        $obj=new showDeck();
        $obj->aggiornaVoto($_GET['mazzo'],$_GET['like']);
        //I TRIGGER AGGIORNANO LIKES IN DECK
        header('Location:mostraMazzo.php?mazzo='.$_GET['mazzo']);
    }
    else
    {
        if (isset($_GET['mazzo']))
            header('Location:mostraMazzo.php?mazzo='.$_GET['mazzo']);
        else
            header('Location:mazzi.php');
    }
?>
