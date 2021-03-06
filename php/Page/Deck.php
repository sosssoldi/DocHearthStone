<?php
    namespace php\Page;

    include_once "Page.php";

    use \php\Database\Database;
    use \php\Database\MySQLConnection\MySQLConnection;

    class Deck implements Page {

        private $db;
        private $errore = array(
                            "",
                            "Errore: Sono stati inseriti dei caratteri non corretti nel nome",
                            "Errore: Questa classe eroe non esiste",
                            "Errore: Il nome non può contenere caratteri sbagliati e la classe eroe deve essere una delle 9 selezionabili",
                            "Errore: Nel campo del costo minimo deve essere inserito un numero (maggiore di zero)",
                            "Errore: Il nome non può contenere caratteri sbagliati e il costo minimo deve essere un numero (maggiore di zero)",
                            "Errore: La classe eroe deve essere una tra quelle selezionabili e il costo minimo deve essere un numero (maggiore di zero)",
                            "Errore: Nome, Classe e costo minimo contengono dei dati sbagliati",
                            "Errore: Nel campo del costo massimo deve essere inserito un numero (maggiore di zero e maggiore del costo minimo)",
                            "Errore: Il nome contiene caratteri sbagliati e il costo massimo deve essere un numero (maggiore di zero e del costo minimo)",
                            "Errore: La classe eroe specificata non esiste e il costo massimo deve &egrave; sbagliato, deve essere un numero",
                            "Errore: Nome, Classe e Costo massimo sono sbagliati",
                            "Errore: I costi sono sbagliati, devono essere dei numeri e il costo massimo deve essere maggiore del costo minimo",
                            "Errore: Nome, costo minimo e costo massimo sono sbagliati",
                            "Errore: Classe, costo minimo e costo massimo sono sbagliati",
                            "Errore: Nome, Classe, costo minimo e costo massimo sono sbagliati. Ricontrolla i dati inseriti"
                            );

    	public function __construct() {
    		$this->db = new Database(new MySQLConnection());
    	}

        public function header() {
            $head = file_get_contents("html/header.html");

            //controllo utente loggato
            if(isset($_SESSION["username"])) {
                $head = str_replace(":login:",
                '<li><a href="user.php">'.$_SESSION["username"].'</a></li>', $head);
				$head = str_replace(":utente:",
                    '<form id="logout" action="logout.php" method="get">
                        <span>'.$_SESSION["username"].'</span>
                        <input id="logoutButton" type="submit" value="Logout" />
                    </form>'
            ,$head);
            }
            else {
                $head = str_replace(":login:",
                '<li lang="en"><a href="login.php">LOGIN</a></li>', $head);
				$head = str_replace(":utente:",'',$head);
            }

            //here
            $head = str_replace('<li><a href="mazzi.php">MAZZI</a></li>',
            '<li class="here">MAZZI</li>', $head);

            echo $head;
        }

    	public function content() {
            $contenuto=file_get_contents("html/mazzi.html");
            $err = 0;
            //controllo che i dati nella query string siano contenuti nei valori prestabiliti
			if(isset($_GET['nome'])) {
                if(strstr($_GET['nome'], '>') || strstr($_GET['nome'], '<'))
                    $err = 1;

                $nome = $_GET["nome"];
                $_GET['nome'] = str_replace("<", "&lt;", $_GET["nome"]);
                $_GET['nome'] = str_replace(">", "&gt;", $_GET["nome"]);
                $_GET['nome'] = str_replace("'", "\'", $_GET["nome"]);

    			$contenuto = str_replace(':nomeMazzo:',
    			'<input id="insertNome" type="text" name="nome" value="'.$nome.'" />', $contenuto);
    		}
    		else {
    			$contenuto = str_replace(':nomeMazzo:',
    			'<input id="insertNome" type="text" name="nome" />', $contenuto);
    		}

            if($this->admin())
    			$contenuto=str_replace(':admin:','<th class="username">Elimina</th>',$contenuto);
    		else
    			$contenuto=str_replace(':admin:','',$contenuto);


            $heroes = array("Tutte le classi","Mago","Sacerdote","Druido","Cacciatore","Guerriero","Paladino","Stregone","Sciamano","Ladro");
            $found = 0;
            if(isset($_GET['classe'])) {
                foreach ($heroes as $h)
                    if($h == $_GET["classe"])
                        $found = 1;
                if($found == 0)
                    $err += 2;
    			$contenuto = str_replace('<option>'.$_GET['classe'].'</option>',
    			'<option selected="selected">'.$_GET['classe'].'</option>', $contenuto);
    		}

            if(isset($_GET['costoMax']) && $_GET['costoMax']!="") {
                if(!is_numeric($_GET["costoMax"]) || $_GET["costoMax"] < 0)
                    $err += 4;
                $contenuto= str_replace(':costoMax:',
                '<input id="insertCostoMax" type="number" min="0" max="100000" name="costoMax" value="'.$_GET['costoMax'].'" />', $contenuto);
            }
            else {
                $contenuto= str_replace(':costoMax:',
                '<input id="insertCostoMax" type="number" min="0" max="100000" name="costoMax" />', $contenuto);
            }

            if(isset($_GET['costoMin']) && $_GET['costoMin']!="") {
                if(isset($_GET["costoMax"]) && $_GET["costoMax"]!="") {
                    if(!is_numeric($_GET["costoMin"]) || $_GET["costoMin"] < $_GET["costoMax"])
                        $err += 8;
                } else {
                    if(!is_numeric($_GET["costoMin"]) || $_GET["costoMin"] < 0)
                        $err += 8;
                }
                $contenuto= str_replace(':costoMin:',
                '<input id="insertCostoMin" type="number" min="0" max="100000" name="costoMin" value="'.$_GET['costoMin'].'" />', $contenuto);
            }
            else {
                $contenuto= str_replace(':costoMin:',
                '<input id="insertCostoMin" type="number" min="0" max="100000" name="costoMin" />', $contenuto);
            }

            $contenuto = str_replace(":errore:","<p class=\"errore\">".$this->errore[$err]."</p>",$contenuto);
            if($err == 0)
                $contenuto=str_replace(':tabellamazzi:',$this->mostraDeck(),$contenuto);
            else
                $contenuto=str_replace(':tabellamazzi:',"",$contenuto);
            echo $contenuto;
        }

    	public function footer() {
			echo file_get_contents("html/footer.html");
        }

        public function costoDeck($deck) {

			$query='SELECT sum(R.r_craft) as costo FROM rarity R join card C on (R.name=C.rarity) join card_deck CD on (C.card_id=CD.card_id)
                    join deck D on (D.deck_id=CD.deck_id) WHERE D.deck_id='.$deck;

            $this->db->query($query);
            $rs=$this->db->resultset($query);

            return $rs;
        }

		//stampa i mazzi in base ai valori ricevuti nella query string
        public function mostraDeck() {

			$query='SELECT D.deck_id as Id, D.name as NomeDeck, H.type as Nome, D.likes as Likes, D.creation_date as Data FROM deck D join hero H on (D.hero_id=H.hero_id) ';

            if(isset($_GET['nome']) && $_GET['nome']!="" && isset($_GET['classe']) && $_GET['classe'] != "Tutte le classi")
                $query.="WHERE D.name LIKE '%".$_GET['nome']."%'  AND H.type LIKE '".$_GET['classe']."' ";
            else
                if(isset($_GET['nome']) && $_GET['nome']!="")
                    $query.="WHERE D.name LIKE '%".$_GET['nome']."%' ";
                else
                    if(isset($_GET['classe']) && $_GET['classe']!="Tutte le classi")
                        $query.="WHERE H.type LIKE '".$_GET['classe']."' ";

            if(isset($_GET['costoMin']) && $_GET['costoMin'] != "")
                $costomin=$_GET['costoMin'];
            else
                $costomin=100000;

            if(isset($_GET['costoMax']) && $_GET['costoMax'] != "")
                $costomax=$_GET['costoMax'];
            else
                $costomax=0;

            $this->db->query($query);
            $rs=$this->db->resultset($query);

            $final="";

			foreach ($rs as $row) {

			   $costoDeck=$this->costoDeck($row['Id']);

                if ($costoDeck[0]['costo'] < $costomin && $costoDeck[0]['costo'] > $costomax) {
                    $final .= '<tr>';
                    $final .= '<td headers="no"><a href="mostraMazzo.php?mazzo='.$row['Id'].'">'.$row['NomeDeck'].'</a></td>';
                    $final .= '<td headers="cl" class="'.$row["Nome"].'">'.$row['Nome'].'</td>';
                    $final .= '<td headers="co" >'.$costoDeck[0]['costo'].'</td>';

					if($row['Likes'] > 0)
						$final .= '<td headers="ra" class="positivo">+'.$row['Likes'].'</td>';

					if($row['Likes'] < 0)
						$final .= '<td headers="ra" class="negativo">'.$row['Likes'].'</td>';

					if($row['Likes'] == 0)
						$final .= '<td headers="ra" >'.$row['Likes'].'</td>';

					$final .= '<td headers="dc" ><time datetime="'.$row["Data"].'">'.$row['Data'].'</time></td>';
                    if($this->admin())
        				$final .= '<td><a href="eliminaMazzo.php?mazzo='.$row['Id'].'">
        				<img class="delete" src="images/icon/remove.png" alt="Elimina guida" /></a></td>';
                    $final .= '</tr>';
				}
			}

            return $final;
        }

		public function eliminaMazzo($id, $status) {

			$query = 'SELECT user_name FROM deck WHERE deck_id = '.$id;

			if($status == 2)
                $query .= ' AND user_name = "'.$_SESSION['username'].'"';

			$this->db->query($query);
			$rs = $this->db->resultset();

			//Se la query ha una riga allora l'username ha anche creato la guida
			if($this->db->rowCount() > 0) {
				//cancello i like in deck_like
				$query = 'DELETE FROM deck_like WHERE deck_id = '.$id;
				$this->db->query($query);
				$this->db->execute();

				//cancello i suggest legati al mazzo
				$query = 'DELETE FROM suggest WHERE deck_id = '.$id;
				$this->db->query($query);
				$this->db->execute();

				//cancello le carte legate al deck da deck_card
				$query = 'DELETE FROM card_deck WHERE deck_id = '.$id;
				$this->db->query($query);
				$this->db->execute();

				//cancello il deck
				$query = 'DELETE FROM deck WHERE deck_id = '.$id;
				$this->db->query($query);
				$this->db->execute();
			}
		}

        private function admin() {
            if(isset($_SESSION["username"]) && $_SESSION["username"] == 'admin')
                return true;
            else return false;
        }
    }
?>
