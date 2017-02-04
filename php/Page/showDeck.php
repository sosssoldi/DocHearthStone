<?php
    namespace php\Page;

    include_once "Page.php";

    use \php\Database\Database;
    use \php\Database\MySQLConnection\MySQLConnection;

    class showDeck implements Page {

        private $db;

    	public function __construct() {
    		$this->db = new Database(new MySQLConnection());
    	}

        public function header() {

            $head = file_get_contents("html/header.html");

            //controllo utente loggato
            if(isset($_SESSION["username"])) {
                $head = str_replace(":login:",'<li><a href="user.php">'.$_SESSION["username"].'</a></li>', $head);
                $head = str_replace(":utente:",
                    '<form id="logout" action="logout.php" method="get">
                        <span>'.$_SESSION["username"].'</span>
                        <input id="logoutButton" type="submit" value="Logout">
                    </form>', $head);
            }
            else {
                $head = str_replace(":login:",'<li lang="en"><a href="login.php">LOGIN</a></li>', $head);
                $head = str_replace(":utente:","", $head);
            }


            echo $head;
        }

        private function bottoni($content) {
            if (isset($_SESSION["username"]))
            {
                $content=str_replace(':bottone+:',
                    '<a href="gestisciVoto.php?mazzo='.$_GET['mazzo'].'&like=1"><button type="submit" class="like">
                        <img src="images/+.jpg" width="20%" align=”middle”>
                    </button></a>',$content);
                $content=str_replace(':bottone-:',
                    '<a href="gestisciVoto.php?mazzo='.$_GET['mazzo'].'&like=0"><button type="submit" class="like">
                        <img src="images/-.png" width="20%" align=”middle”>
                    </button></a>',$content);
            }
            else {
                $content=str_replace(':bottone+:',
                    '<button type="submit" class="like">
                        <img src="images/+.jpg" width="20%" align=”middle”>
                    </button>',$content);
                $content=str_replace(':bottone-:',
                    '<button type="submit" class="like">
                        <img src="images/-.png" width="20%" align=”middle”>
                    </button>',$content);
            }
            return $content;
        }

    	public function content() {
            $content=file_get_contents("html/mostraMazzo.html");

            $content=$this->bottoni($content);

            $rs=$this->trovaInfo($_GET['mazzo']);

			if(count($rs) == 0 && !is_numeric($_GET["mazzo"]))
				header("Location: mazzi.php");

            $content=str_replace(':NomeEroe:',$rs[0]['Hero'],$content);
            $content=str_replace(':valutazioneMazzo:',$rs[0]['Likes'],$content);
            $content=str_replace(':nomeMazzo:',$rs[0]['Nome'],$content);
            $content=str_replace(':creazioneMazzo:',$rs[0]['Data'],$content);
            $content=str_replace(':votiTotali:',$this->votiTot($_GET['mazzo']),$content);
            $content=str_replace(':descrizioneMazzo:',$rs[0]['Descr'],$content);

            $obj=new Deck();
            $costo=$obj->costoDeck($_GET['mazzo']);

            $content=str_replace(':costoMazzo:',$costo[0]['costo'],$content);

            $content=str_replace(':tabellacarte:',$this->getCarte($_GET['mazzo']),$content);

            if (isset($_SESSION['username']))
                $content=str_replace(':formCommenti:',
                            '<form method="post" action="mostraMazzo.php?mazzo=:idmazzo:">
        			                <label for="area">Inserisci un commento</label>
        			                <textarea id="area" name="commento"></textarea>
        			                <input type="submit" value="COMMENTA" />
        		            </form>'
                            ,$content);
            else
                $content=str_replace(':formCommenti:','',$content);

            $content=str_replace(':idmazzo:',$_GET['mazzo'],$content);

            if (isset($_POST['commento']) && $_POST['commento']!="")
            {
                $_POST["commento"]=htmlspecialchars($_POST["commento"]);
                $_POST["commento"]=str_replace("'","\'",$_POST["commento"]);
                $query='INSERT INTO suggest VALUES ("","'.$_POST['commento'].'","'.$_SESSION['username'].'",'.$_GET['mazzo'].')';
                $this->db->query($query);
                $this->db->execute($query);
            }

            $content=str_replace(':tabellacommenti:',$this->Commenti($_GET['mazzo']),$content);

            echo $content;
        }

    	public function footer() {
            echo file_get_contents("html/footer.html");
        }

        public function trovaInfo($mazzo) {
            $query='SELECT D.name as Nome, D.likes as Likes, D.creation_date as Data, D.description as Descr, H.type as Hero
                    FROM deck D join hero H on (D.hero_id=H.hero_id)
                    WHERE D.deck_id='.$mazzo;

            $this->db->query($query);
            $rs=$this->db->resultset();

            return $rs;
        }

        public function getCarte($mazzo)
        {
            $query='SELECT C.card_id as id, C.mana as Mana, C.name as Nome, C.expansion_name as Tipo, C.rarity as R, count(*) as Q
                    FROM card C join card_deck CD on (C.card_id=CD.card_id)
                    WHERE CD.deck_id='.$mazzo.' GROUP BY C.name, C.c_type, C.rarity ORDER by Mana';

            $this->db->query($query);
            $rs=$this->db->resultset();

            $final="";
            foreach ($rs as $row) {

                switch ($row['Tipo']) {
                    case 'EXPERT1':
                        $row['Tipo']='Classico';
                        break;
                    case 'CORE':
                        $row['Tipo']='Set Base';
                        break;
                    case 'BRM':
                        $row['Tipo']='MR';
                        break;
                    case 'OG':
                        $row['Tipo']='SdA';
                        break;
                    case 'TGT':
                        $row['Tipo']='iGT';
                        break;
                    case 'LOE':
                        $row['Tipo']='LdE';
                        break;
                    case 'GVG':
                        $row['Tipo']='Gvg';
                        break;
                    case 'GANGS':
                        $row['Tipo']='Gangs';
                        break;
                    case 'KARA':
                        $row['Tipo']='Karazhan';
                        break;
                }

                $final.='<tr>';
                $final.='<td>'.$row['Mana'].'</td>';
                $final.='<td class="'.$row['R'].'" onmouseover="showImg(this, \''.$row["id"].'\');" onmouseout="hideImg(this);">'.$row['Nome'].'</td>';
                $final.='<td>'.$row['Tipo'].'</td>';
                $final.='<td>x'.$row['Q'].'</td>';
                $final.='</tr>';
            }

            return $final;
        }

        public function votiTot($mazzo) {
            $query='SELECT count(*) as V
                    FROM deck_like
                    WHERE deck_like.deck_id='.$mazzo;

            $this->db->query($query);
            $rs=$this->db->resultset();

            return $rs[0]['V'];
        }

        public function Commenti($mazzo) {
            $query='SELECT S.user_name as U, S.content as C
                    FROM suggest S
                    WHERE S.deck_id='.$mazzo;

            $this->db->query($query);
            $rs=$this->db->resultset();

            $final="";
            foreach ($rs as $row) {
                $final.='<tr>';
                $final.='<td>'.$row['U'].'</td>';
                $final.='<td>'.$row['C'].'</td>';
                $final.='</tr>';
            }

            return $final;

        }

        public function aggiornaVoto($mazzo, $like)
        {
            if ($like>0)
                $voto=1;
            else
                $voto=0;
            $query='SELECT DL.vote as Voto
                    FROM deck_like DL
                    WHERE DL.user_name="'.$_SESSION['username'].'" AND DL.deck_id='.$mazzo;

            $this->db->query($query);
            $rs=$this->db->resultset();

            if ($this->db->rowCount()>0)
            {
                if($voto!=$rs[0]['Voto'])
                {
                    $query='UPDATE deck_like
                            SET deck_like.vote='.$voto.'
                            WHERE deck_like.user_name="'.$_SESSION['username'].'" AND deck_like.deck_id='.$mazzo;
                    $this->db->query($query);
                    $this->db->execute($query);
                }
            }
            else
            {
                $query='INSERT INTO deck_like VALUES ("'.$_SESSION['username'].'",'.$voto.','.$mazzo.')';
                $this->db->query($query);
                $this->db->execute($query);
            }
        }

    }
?>
