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

            $contenuto=str_replace(':tabellaMazzi:',$this->mostraDeck(),$contenuto);

            echo $contenuto;
        }

    	public function footer() {

        }

        public function mostraDeck() {
            $query='SELECT D.deck_id, D.name, H.name, sum(R.r_craft) as Costo, D.likes, D.creation_date';
            $query.='FROM card as C join card_deck as CD on (C.card_id=CD.card_id) join deck as D on (CD.deck_id=D.deck_id)
                    join hero as H on (D.hero_id=H.hero_id) join rarity as R on (R.name=C.rarity)';

            if(isset($_GET['nome']))
            $query.="WHERE D.name LIKE '%".$_GET['nome']."%' AND  H.name LIKE '%".$_GET['classe']."%' ";
            $query.='GROUP BY D.deck_id';
            $this->db->query($query);
            $rs=$this->db->resultset($query);

            $final="";
            foreach ($rs as $row) {
                if ($row['Costo']<$_GET['CostoMin'] && $row['Costo']>$_GET['CostoMax'])
                {
                    $final.=
                    "<tr>
                        <td>{$row['D.deck_id']}</td>
                        <td class=\"{$row['H.name']}\">{$row['H.name']}</td>
                        <td>{$row['Costo']}</td>
                        <td>{$row['D.likes']}</td>
                        <td>{$row['D.creation_date']}</td>
                    </tr>";
                }
            }
            return $final;
        }

    }
?>
