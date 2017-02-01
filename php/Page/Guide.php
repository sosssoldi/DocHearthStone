<?php
    namespace php\Page;

    include_once "Page.php";

    class Guide implements Page {
        public function header() {
            $head = file_get_contents("html/header.html");

            //controllo utente loggato
            if(isset($_SESSION["username"])) {
                $head = str_replace(":login:",
                '<li><a href="user.php">'.$_SESSION["username"].'</a></li>', $head);
            }
            else {
                $head = str_replace(":login:",
                '<li lang="en"><a href="login.php">LOGIN</a></li>', $head);
            }

            //here
            $head = str_replace('<li><a href="mazzi.php">GUIDE</a></li>',
            '<li class="here">GUIDE</li>', $head);

            echo $head;
        }
        public function content() {
            $content=file_get_contents("html/guide.html");

            //$content=str_replace(":tabellamazzi",funzione per trovare mazzi,$content);

            echo $content;
        }
        public function footer() {

        }
    }
?>
