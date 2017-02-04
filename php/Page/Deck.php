<?php
    namespace php\Page;

    include_once "Page.php";

    use \php\Database\Database;
    use \php\Database\MySQLConnection\MySQLConnection;

    class Deck implements Page {

        private $db;

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

            //here
            $head = str_replace('<li><a href="mazzi.php">MAZZI</a></li>',
            '<li class="here">MAZZI</li>', $head);

            echo $head;
        }

    	public function content() {
            $contenuto=file_get_contents("html/mazzi.html");

            if(isset($_GET['nome'])) {
    			$contenuto = str_replace(':nomeMazzo:',
    			'<input type="text" name="nome" value="'.$_GET['nome'].'"/>', $contenuto);
    		}
    		else {
    			$contenuto = str_replace(':nomeMazzo:',
    			'<input type="text" name="nome" />', $contenuto);
    		}

            if(isset($_GET['classe'])) {
    			$contenuto = str_replace('<option>'.$_GET['classe'].'</option>',
    			'<option selected>'.$_GET['classe'].'</option>', $contenuto);
    		}

            if(isset($_GET['costoMax'])) {
                $contenuto= str_replace(':costoMax:',
                '<input type="number" min="0" max="100000" name="costoMax" value="'.$_GET['costoMax'].'"/>', $contenuto);
            }
            else {
                $contenuto= str_replace(':costoMax:',
                '<input type="number" min="0" max="100000" name="costoMax" />', $contenuto);
            }

            if(isset($_GET['costoMin'])) {
                $contenuto= str_replace(':costoMin:',
                '<input type="number" min="0" max="100000" name="costoMin" value="'.$_GET['costoMin'].'"/>', $contenuto);
            }
            else {
                $contenuto= str_replace(':costoMin:',
                '<input type="number" min="0" max="100000" name="costoMin" />', $contenuto);
            }

            $contenuto=str_replace(':tabellamazzi:',$this->mostraDeck(),$contenuto);

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

        public function mostraDeck() {

			$query='SELECT D.deck_id as Id, D.name as NomeDeck, H.type as Nome, D.likes as Likes, D.creation_date as Data FROM deck D join hero H on (D.hero_id=H.hero_id) ';

            if(isset($_GET['nome']) && $_GET['nome']!="" && isset($_GET['classe']) && $_GET['classe'] != "")
                $query.="WHERE D.name LIKE '%".$_GET['nome']."%'  AND H.type LIKE '".$_GET['classe']."' ";
            else
                if(isset($_GET['nome']) && $_GET['nome']!="")
                    $query.="WHERE D.name LIKE '%".$_GET['nome']."%' ";
                else
                    if(isset($_GET['classe']) && $_GET['classe']!="")
                        $query.="WHERE H.type LIKE '".$_GET['classe']."' ";

            $query.='LIMIT 40';

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
                    $final .= '<td><a href="mostraMazzo.php?mazzo='.$row['Id'].'">'.$row['NomeDeck'].'</a></td>';
                    $final .= '<td class="'.$row['Nome'].'">'.$row['Nome'].'</td>';
                    $final .= '<td>'.$costoDeck[0]['costo'].'</td>';

					if($row['Likes'] > 0)
						$final .= '<td class="positivo">+'.$row['Likes'].'</td>';

					if($row['Likes'] < 0)
						$final .= '<td class="negativo">'.$row['Likes'].'</td>';

					if($row['Likes'] == 0)
						$final .= '<td>'.$row['Likes'].'</td>';

					$final .= '<td>'.$row['Data'].'</td>';
                    $final .= '</tr>';
				}
			}

            return $final;
        }
		
		public function eliminaMazzo($id) {
			
			$query = 'SELECT user_name FROM deck WHERE user_name = "'.$_SESSION['username'].'" AND deck_id = '.$id;
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
    }
?>
