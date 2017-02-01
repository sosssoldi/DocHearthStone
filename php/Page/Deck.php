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
            }
            else {
                $head = str_replace(":login:",
                '<li lang="en"><a href="login.php">LOGIN</a></li>', $head);
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

        }

        public function costoDeck($deck) {
            $query='SELECT sum(R.r_craft) as costo';
            $query.=' FROM rarity R join card C on (R.name=C.rarity) join card_deck CD on (C.card_id=CD.card_id) join deck D on (D.deck_id=CD.deck_id)
                    WHERE D.deck_id='.$deck;

            $this->db->query($query);
            $rs=$this->db->resultset($query);

            return $rs;
        }

        public function mostraDeck() {
            $query='SELECT D.deck_id as Id, D.name as NomeDeck, H.name as Nome, D.likes as Likes, D.creation_date as Data ';
            $query.='FROM deck D join hero H on (D.hero_id=H.hero_id) ';

            if(isset($_GET['nome']) && $_GET['nome']!="" && isset($_GET['classe']) && $_GET['classe'])
                $query.="WHERE D.name LIKE '%".$_GET['nome']."%'  AND H.name LIKE '%".$_GET['classe']."%' ";
            else
                if(isset($_GET['nome']) && $_GET['nome']!="")
                    $query.="WHERE D.name LIKE '%".$_GET['nome']."%' ";
                else
                    if(isset($_GET['classe']) && $_GET['classe']!="")
                        $query.="WHERE H.name LIKE '%".$_GET['classe']."%' ";

            $query.='LIMIT 40';
            echo $query;

            if(isset($_GET['costoMin']))
                $costomin=$_GET['costoMin'];
            else
                $costomin=100000;

            if(isset($_GET['costoMax']))
                $costomax=$_GET['costoMax'];
            else
                $costomax=0;

            $this->db->query($query);
            $rs=$this->db->resultset($query);

            $final="";
            foreach ($rs as $row) {
               $costoDeck=$this->costoDeck($row['Id']);

                /*if ($costoDeck<$costomin && $costoDeck>$costomax)*/

                    $final.=
                    '<tr>
                        <td>'.$row['NomeDeck'].'</td>
                        <td class=" '.$row['Nome'].'">'.$row['Nome'].'</td>
                        <td>'.$costoDeck['costo'].'</td>
                        <td>'.$row['Likes'].'</td>
                        <td>'.$row['Data'].'</td>
                    </tr>';

            }
            return $final;
        }

    }
?>
