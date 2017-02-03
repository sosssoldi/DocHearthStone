<?php
    namespace php\Page;

    include_once "Page.php";

    use \php\Database\Database;
    use \php\Database\MySQLConnection\MySQLConnection;

    class AggGuida implements Page {

        private $db;
        private $status = 0;
        /*
        status = 0 -> nessun tentativo di aggiungere guida
        status = 1 -> errore: titolo della guida mancante
        status = 2 -> errore: testo della guida mancante
        status = 3 -> errore testo e titolo mancanti
        status = 4 -> inserimento guida
        status = 5 -> database ha avuto problemi
        */

    	public function __construct() {
    		$this->db = new Database(new MySQLConnection());
    	}

        public function header() {
            $head = file_get_contents("html/header.html");

            $this->controlloInserimento();

            if ($this->status == 4)
                $this->inserimento();

            //controllo utente loggato
            if(isset($_SESSION["username"])) {
                $head = str_replace(":login:",
                '<li><a href="user.php">'.$_SESSION["username"].'</a></li>', $head);
				$head = str_replace(":utente:",
                '<div id="boxutente">
                    <span>'.$_SESSION["username"].'</span>
                    <a href="logout.php"><button>Logout</button></a>
                </div>'
            ,$head);
            }
            else {
                $head = str_replace(":login:",
                '<li lang="en"><a href="login.php">LOGIN</a></li>', $head);
				$head = str_replace(":utente:",'',$head);
            }

            echo $head;
        }

        public function content() {
            $content=file_get_contents("html/aggGuida.html");

            $content = $this->cambiaLabel($content);

            switch ($this->status) {
                case 1:
                    $content = $this->recuperaT($content,1);
                    break;
                case 2:
                    $content = $this->recuperaT($content,2);
                    break;
                case 5:
                    $content = $this->recuperaT($content,3);

            }

            echo $content;
        }

        private function cambiaLabel($c) {

            switch($this->status) {
    			case 0:
    				$c = str_replace(':segnalaErrore:','',$c);
    				break;
    			case 1:
    				$c = str_replace(':segnalaErrore:','<p>Titolo della guida assente.</p>',$c);
    				break;
    			case 2:
    				$c = str_replace(':segnalaErrore:','<p>Testo della guida assente.</p>',$c);
    				break;
    			case 3:
    				$c = str_replace(':segnalaErrore:','<p>Testo e titolo della guida assenti.</p>',$c);
    				break;
                case 5:
                    $c = str_replace(':segnalaErrore:','<p>Il server ha riscontrato un errore, prova a reinserirla.</p>',$c);
            }
                return $c;
        }

        private function controlloInserimento() {
            if(isset($_GET['titolo']) && isset($_GET['testo'])){
                if($_GET['titolo'] == "")
                    $this->status = 1;
                if ($_GET['testo'] == "")
                    if($this->status == 1)
                        $this->status = 3;
                    else
                        $this->status = 2;
                else if($_GET['titolo'] != "")
                    $this->status = 4;
            }
        }

        //recupero il testo prima inserito in caso di dati mancanti
        private function recuperaT($c,$s){
            if($s == 1)
                $c = str_replace('<textarea maxlength="5000" required autocomplete="off" name="testo"></textarea>','<textarea maxlength="5000" required autocomplete="off" name="testo">'.$_GET['testo'].'</textarea>',$c);
            else if (s == 2)
                $c = str_replace('<input type="text" required autocomplete="off" name="titolo"/>','<input type="text" required autocomplete="off" name="titolo" value="'.$_GET['titolo'].'"/>',$c);
            else{
                $c = str_replace('<textarea maxlength="5000" required autocomplete="off" name="testo"></textarea>','<textarea maxlength="5000" required autocomplete="off" name="testo">'.$_GET['testo'].'</textarea>',$c);
                $c = str_replace('<input type="text" required autocomplete="off" name="titolo"/>','<input type="text" required autocomplete="off" name="titolo" value="'.$_GET['titolo'].'"/>',$c);
            }

            return $c;
        }

        private function inserimento(){

            $hero = 'SELECT hero_id FROM hero WHERE type = "'.$_GET['scelta'].'"';
            $this->db->query($query);
            $rs = $this->db->resultset();

            $query = 'INSERT into guide VALUES("","'.$_GET['titolo'].'","'.$_GET['testo'].'","","'$rs['hero_id']'","'$_SESSION['username']'"');

            $this->db->query($query);
            $r = $this->db->execute();

            if($r == 1)
                header("Location: user.php");
            else
                $this->status = 5;
        }


        public function footer() {
            echo file_get_contents("html/footer.html");
        }
}