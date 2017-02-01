<?php
    session_start();
    if (isset($_SESSION["username"])) {
        header("Location: index.php");
    }
    include_once 'autoloader.php';
    use \php\Page\Login;
    echo file_get_contents("html/login_head.html");
    $obj = new Login();
    $obj->header();
    $obj->content();
    $obj->footer();
    echo file_get_contents("html/chiudi.html");
?>
