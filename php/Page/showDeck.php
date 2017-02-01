<?php
    namespace php\Page;

    include_once "Page.php";

    class showDeck implements Page {
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
            /*$head = str_replace('<li lang="en"><a href="index.php">HOMEPAGE</a></li>',
            '<li lang="en" class="here">HOMEPAGE</li>', $head);*/

            echo $head;
        }
    	public function content() {
            $content=file_get_contents("html/mostraMazzo.html");

            //bisogna fare: immagine_classe, valutazione mazzo, nome costo creazione e voti mazzo,
            //la descrizione, tabella x carte e commenti e implementare il pulsante aggiungi commento

            echo $content;
        }
    	public function footer() {

        }

    }
?>
