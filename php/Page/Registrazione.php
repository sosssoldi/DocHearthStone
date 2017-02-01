<?php
    namespace php\Page;

    include_once "Page.php";

    class Registrazione implements Page {
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

            echo $head;
        }
        public function content() {
            $content=file_get_contents("html/registrazione.html");

            echo $content;
        }
        public function footer() {

        }
    }
?>
