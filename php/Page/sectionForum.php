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
					'<form id="logout" action="logout.php" method="get">
	                    <span>'.$_SESSION["username"].'</span>
	                    <input id="logoutButton" type="submit" value="Logout" />
	                </form>'
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

		//content per la ricerca
        public function content2() {

            $content=file_get_contents("html/sezioneForum.html");

            $content=str_replace(':nomesezione:',"Risultato della ricerca:",$content);

            if($this->admin())
                $content=str_replace(':admin:','<th class="numpost">Elimina</th>',$content);
            else
                $content=str_replace(':admin:','',$content);


            $result = $this->getRicerca();
            if($result !== false) {
                $content=str_replace(':contenuto:',$result,$content);
                $content=str_replace(':sezioneCommenti:','',$content);
            } else {
                $content=str_replace(':contenuto:','',$content);
                $content=str_replace(':sezioneCommenti:','<p class="centrato">La ricerca non ha prodotto risultati.</p>',$content);
            }
            $content=str_replace(':nomesection:','',$content);

            echo $content;
        }

        public function content() {

            $content=file_get_contents("html/sezioneForum.html");

            if($this->admin())
                $content=str_replace(':admin:','<th class="numpost">Elimina</th>',$content);
            else
                $content=str_replace(':admin:','',$content);

            if (isset($_POST['Titolo']) && $_POST['Titolo']!="") {
                $_POST["Titolo"]=htmlspecialchars($_POST["Titolo"]);
                $_POST["Titolo"]=str_replace("'","\'",$_POST["Titolo"]);
                $_POST["Titolo"]=str_replace("<","&lt;",$_POST["Titolo"]);
                $_POST["Titolo"]=str_replace(">","&gt;",$_POST["Titolo"]);
                $this->InserisciTopic();
            }

            $content=str_replace(':nomesezione:',$_GET['nome'],$content);

            $content=str_replace(':contenuto:',$this->getTopic(),$content);

            $content = $this->commentoUserLoggato($content);

			$pagina = str_replace(' ', '%20', $_GET['nome']);
            $content=str_replace(':nomesection:', $pagina, $content);

            echo $content;
        }

        //se l'utente è loggato mostra la possibilità di aggiungere un topic alla sezione
        private function commentoUserLoggato($content) {

            if (isset($_SESSION['username']))
                $content=str_replace(':sezioneCommenti:', '<form method="post" action="sezioneForum.php?nome=:nomesection:">
            		<label id="labelAdd">Aggiungi una Discussione</label>
            		<fieldset class="lab">
            			<label id="labelTit" for="titolo">Titolo</label>
            			<input type="text" id="titolo" required="required" autocomplete="off" name="Titolo" />
            		</fieldset>
            		<fieldset class="lab">
            			<label id="labelText" for="area">Descrizione</label>
            			<textarea id="area" name="Commento"></textarea>
					</fieldset>
					<input type="submit" value="AGGIUNGI" />
            	</form>',$content);
            else {
                $content=str_replace(':sezioneCommenti:','',$content);
            }
            return $content;
        }

        public function footer() {
            echo file_get_contents("html/footer.html");
        }

        //ritorna l'username dell'utente che ha aggiunto un commento per ultimo in un topic
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

        //crea codice HTML per mostrare tutti i topic di una sezione
        public function getTopic()
        {
            $query='SELECT T.topic_id as Id, T.title as Titolo, T.user_name as Creatore, T.num_comments as N
                    FROM section S join topic T on (S.section_id=T.section_id)
                    WHERE S.name="'.$_GET['nome'].
                    '" ORDER BY T.topic_id';

            $this->db->query($query);
            $rs = $this->db->resultset();

            $final="";
            foreach ($rs as $row) {
                $user=$this->getUser($row['Id']);
                $final.='<tr>';
                $final.='<td headers="to" class="forum"><a href="discussione.php?id='.$row['Id'].'"><h3>'.$row['Titolo'].'</h3></a>
                            <span>di: '.$row['Creatore'].'</span></td>';
                $final.='<td headers="uc" class="lastpost">'.$user.'</td>';
                $final.='<td headers="co" class="lastpost">'.$row['N'].'</td>';
                if($this->admin())
                    $final .= '<td class="lastpost"><a href="eliminaTopic.php?topic='.$row['Id'].'">
                    <img class="delete" src="images/icon/remove.png" alt="Elimina topic"></a></td>';
                $final.='</tr>';
            }
            return $final;
        }

		//ritorna una stringa contenente codice HTML per la tabella che contiene i topic della ricerca
        public function getRicerca() {
            if($_GET["barra"]=="")
                header("Location: forum.php");

            $pezzi = explode(" ", $_GET['barra']);
            foreach ($pezzi as &$p) {
                $p = htmlspecialchars($p);
                $p = str_replace("'","\'",$p);
            }
            $resultArray = array();

            $query = 'SELECT T.topic_id as Id, T.title as Titolo, T.user_name as Creatore, T.num_comments as N
                    FROM topic T
                    WHERE T.content LIKE "%'.$pezzi[0].'%" OR T.title LIKE "%'.$pezzi[0].'%"';

            for($i=1;$i<count($pezzi);$i++)
                $query .= ' or T.content LIKE "%'.$pezzi[$i].'%" OR T.title LIKE "%'.$pezzi[0].'%"';

            $this->db->query($query);
            $rs = $this->db->resultset();

            $resultArray = array_merge($resultArray, $rs);

            $final="";
            if(count($rs) == 0)
                $final = false;
            else
                foreach ($resultArray as $row) {
                    $user=$this->getUser($row['Id']);
                    $final.='<tr>';
                    $final.='<td headers="to" class="forum"><a href="discussione.php?id='.$row['Id'].'"><h3>'.$row['Titolo'].'</h3></a>
                                <span>di: '.$row['Creatore'].'</span></td>';
                    $final.='<td headers="uc" class="lastpost">'.$user.'</td>';
                    $final.='<td headers="co" class="lastpost">'.$row['N'].'</td>';
                    if($this->admin())
                        $final .= '<td class="lastpost"><a href="eliminaTopic.php?topic='.$row['Id'].'">
                        <img class="delete" src="images/icon/remove.png" alt="Elimina topic" /></a></td>';
                    $final.='</tr>';
                }
            return $final;
        }

        //ritorna l'id della sezione in cui mi trovo
        public function getId(){
            $query='SELECT section_id as Id
                    FROM section
                    WHERE section.name="'.$_GET['nome'].'"';
            $this->db->query($query);
            $rs = $this->db->resultset();

            return $rs[0]['Id'];
        }

        //inserisce il nuovo topic nella tabella topic
        public function InserisciTopic() {
            if(isset($_POST["Commento"])) {
                $_POST["Commento"]=htmlspecialchars($_POST["Commento"]);
                $_POST["Commento"]=str_replace("'","\'",$_POST["Commento"]);
                $_POST["Commento"]=str_replace("<","&lt;",$_POST["Commento"]);
                $_POST["Commento"]=str_replace(">","&gt;",$_POST["Commento"]);
            }
            $data = date ("Y-m-d G:i");
            $id=$this->getId();
            $query='INSERT INTO topic VALUES ("","'.$_POST['Titolo'].'","'.$_POST['Commento'].'","'.$data.'","'.$_SESSION['username'].'",0,'.$id.')';
            $this->db->query($query);
            $this->db->execute($query);
        }

        //controlla se l'user loggato è un admin
        private function admin() {
            if(isset($_SESSION["username"]) && $_SESSION["username"] == 'admin')
                return true;
            else return false;
        }

        //elimina il topic dal database
        public function eliminaTopic($topic) {

            $query = 'DELETE FROM comment WHERE topic_id='.$topic;
            $this->db->query($query);
            $this->db->execute($query);

            $query = 'DELETE FROM topic WHERE topic_id='.$topic;
            $this->db->query($query);
            $this->db->execute($query);
			
			//il numero di topic di una sezione si aggiornano con il trigger del database
        }
    }
?>
