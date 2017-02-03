<?php
    namespace php\Page;

    include_once "Page.php";
    use \php\Database\Database;
    use \php\Database\MySQLConnection\MySQLConnection;

    class sectionForum implements Page {

        private $db = null;

        public function __construct() {
            $this->db = new Database(new MySQLConnection());
        }

        public function header() {
            $head = file_get_contents("html/header.html");

            //controllo utente loggato
            if(isset($_SESSION["username"]))
            {
                $head = str_replace(":login:",
                '<li><a href="user.php">'.$_SESSION["username"].'</a></li>', $head);
				$head = str_replace(":utente:",
                '<div id="boxutente">
                    <span>'.$_SESSION["username"].'</span>
                    <a href="logout.php"><button>Logout</button></a>
                </div>'
                ,$head);
            }
            else
            {
                $head = str_replace(":login:",
                '<li lang="en"><a href="login.php">LOGIN</a></li>', $head);
				$head = str_replace(":utente:",'',$head);
            }

            echo $head;
        }
        public function content() {

            $content=file_get_contents("html/sezioneForum.html");

            if (isset($_POST['Titolo']) && $_POST['Titolo']!="")
                $this->inserisciCommento();

            $content=str_replace(':nomesezione:',$_GET['nome'],$content);

            $content=str_replace(':contenuto:',$this->getCommenti(),$content);

            if (isset($_SESSION['username']))
                $content=str_replace(':sezioneCommenti:', '<form method="post" action="sezioneForum.php?nome=:nomesection:">
            		<label id="labelAdd">Aggiungi una Discussione</label>
            		<div class="lab">
            			<label id="labelTit" for="titolo">Titolo</label>
            			<input type="text" id="titolo" name="Titolo">
            		</div>
            		<div class="lab">
            			<label id="labelText" for="area">Descrizione</label>
            			<textarea id="area" name="Commento"></textarea>
            	  </div>
            		<input type="submit" value="AGGIUNGI" />
            	</form>',$content);
            else {
                $content=str_replace(':sezioneCommenti:','',$content);
            }

            $content=str_replace(':nomesection:',$_GET['nome'],$content);

            echo $content;
        }

        public function footer() {
            echo file_get_contents("html/footer.html");
        }

        public function getUser($id)
        {
            $query='SELECT C.user_name as User
                    FROM comment C join topic T on (C.topic_id=T.topic_id)
                    WHERE T.topic_id='.$id.'
                    ORDER BY C.comment_id DESC
                    LIMIT 1';
            $this->db->query($query);
            $rs = $this->db->resultset();

            if($this->db->rowCount()>0)
                return $rs[0]['User'];
            else
                return null;

        }

        public function getCommenti()
        {
            $query='SELECT T.topic_id as Id, T.title as Titolo, T.user_name as Creatore, T.num_comments as N
                    FROM section S join topic T on (S.section_id=T.section_id)
                    WHERE S.name="'.$_GET['nome'].
                    '" GROUP BY T.title ORDER BY T.topic_id';

            $this->db->query($query);
            $rs = $this->db->resultset();

            $final="";
            foreach ($rs as $row) {
                $user=$this->getUser($row['Id']);
                $final.='<tr>';
                $final.='<td class="forum"><a href="discussione.html"><h3>'.$row['Titolo'].'</h3></a>
                            <span>di: '.$row['Creatore'].'</span></td>';
                $final.='<td class="lastpost">'.$user.'</td>';
                $final.='<td class="lastpost">'.$row['N'].'</td>';
                $final.='</tr>';
            }

            return $final;
        }

        public function getId(){
            $query='SELECT section_id as Id
                    FROM section
                    WHERE section.name="'.$_GET['nome'].'"';
            $this->db->query($query);
            $rs = $this->db->resultset();

            return $rs[0]['Id'];
        }

        public function inserisciCommento()
        {
            $data = date ("Y-m-d G:i");
            $id=$this->getId();
            $query='INSERT INTO topic VALUES ("","'.$_POST['Titolo'].'","'.$_POST['Commento'].'","'.$data.'","'.$_SESSION['username'].'",0,'.$id.')';
            $this->db->query($query);
            $this->db->execute($query);
        }

    }
?>
