<?php
    namespace php\Page;

    include_once "Page.php";

    use \php\Database\Database;
    use \php\Database\MySQLConnection\MySQLConnection;

    class Discussion implements Page {

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

            echo $head;
        }

        public function content() {
            $content=file_get_contents("html/discussione.html");

            //se l'utente è loggato dà la possibilita di aggiungere un commento al topic
            if (isset($_SESSION['username']))
                $content=str_replace(':commento:', '<form method="post" action="discussione.php?id=:idpagina:">
                			<label for="area">Inserisci un commento</label>
                			<textarea id="area" name="area"></textarea>
                			<input id="addCommento" type="submit" value="COMMENTA" />
                		</form>',$content);
            else {
                $content=str_replace(':commento:','',$content);
            }

            if (isset($_POST['area']) && $_POST['area']!="") {
                $_POST["area"]=htmlspecialchars($_POST["area"]);
                $_POST["area"]=str_replace("'","\'",$_POST["area"]);
                $this->inserisciC();
            }

            $content=str_replace(':idpagina:',$_GET['id'],$content);

			$sezione = $this->getSezione();
			$content= str_replace(':sezioneForum:',$sezione,$content);
			$sezione = str_replace (' ','%20',$sezione);
			$content = str_replace(':linkSezione:',$sezione,$content);
			
            $content=str_replace(':nomediscussione:',$this->getDiscussione(),$content);

            $content=str_replace(':op:',$this->getTesto(),$content);

            $content=str_replace(':posts:',$this->getC(),$content);

            echo $content;

        }

        public function footer() {
            echo file_get_contents("html/footer.html");
        }
		
		//ritorna la sezione del topic
		public function getSezione() {
			$query = 'SELECT section.name AS sezione FROM section, topic WHERE section.section_id = topic.section_id AND topic.topic_id = '.$_GET['id'];
			$this->db->query($query);
			$rs = $this->db->resultset();
			return $rs[0]['sezione'];
		}

        //ritorna il titolo del topic selezionato
        public function getDiscussione() {
            $query='SELECT T.title as Titolo
                    FROM topic T
                    WHERE T.topic_id="'.$_GET['id'].'"';

            $this->db->query($query);
            $rs = $this->db->resultset();

            if($this->db->rowCount()==0)
                header("Location: forum.php");
            else
                return $rs[0]['Titolo'];
        }

        //crea codice HTML per la descrizione del topic
        public function getTesto() {

            //query per testo post
            $query = 'SELECT U.username as Nome, U.entry_date as Data, U.count_post as N, U.photo_id as Foto, T.title as Titolo, T.content as Testo, T.creation_date as Creaz
                    FROM user U join topic T on (T.user_name=U.username) WHERE T.topic_id='.$_GET['id'];

            $this->db->query($query);
            $rs = $this->db->resultset();

            $final="";

            foreach ($rs as $row) {
                $final.=
                '<div id="postOP">
                    <div class="dataora">
                        <p><time datetime="'.$row['Creaz'].'">'.$row['Creaz'].'</time></p>
                    </div>
				    <div class="userptext">
                        <div class="user">
					        <img class="fotoProfilo" src="'.$row['Foto'].'" alt="Immagine profilo utente" />
                            <p>'.$row['Nome'].'</p>
                            <p>Data di entrata:</p><span><time datetime="'.$row['Data'].'">'.$row['Data'].'</time></span>
					        <p>Interventi nel forum: '.$row['N'].'</p>
                        </div>
                        <div class="text">';
				
				if($row['Testo'] != "")
                            $final .='<p>'.$row['Testo'].'</p>';
				else
					 $final .='<p>'.$row['Titolo'].'</p>';
   		        
				$final .= 
						'</div>
				    </div>
                </div>';
            }

            return $final;
        }

        //crea codice HTML per tutti i commenti del topic
        public function getC() {

            //query per commenti
            $query = 'SELECT comment_id, U.username as Nome, U.entry_date as Data, U.count_post as N, U.photo_id as Foto, C.content as Commento, C.creation_date as DataC
                    FROM user U join comment C on (C.user_name=U.username) join topic T on (T.topic_id=C.topic_id)
                    WHERE T.topic_id='.$_GET['id'];

            $this->db->query($query);
            $rs = $this->db->resultset();

            $final="";

            foreach ($rs as $row) {
                $final.=
                '<div class="post">
                    <div class="dataora">
                        <p><time datetime="'.$row['DataC'].'">'.$row['DataC'].'</time></p>
                    </div>
				    <div class="userptext">
                        <div class="user">
					        <img class="fotoProfilo" src="'.$row['Foto'].'" alt="Immagine profilo utente" />
                            <p>'.$row['Nome'].'</p>
                            <p>Data di entrata:</p><span><time datetime="'.$row['Data'].'">'.$row['Data'].'</time></span>
					        <p>Interventi nel forum: '.$row['N'].'</p>
                        </div>
                        <div class="text">
                            <p>'.$row['Commento'].'</p>
   		                </div>
				    </div>';
				if(isset($_SESSION['username']) && $_SESSION['username'] == 'admin')
					$final .= '<a href="eliminaCommento.php?commento='.$row['comment_id'].'&discussione='.$_GET['id'].'"><img src="images/icon/remove.png" alt="Elimina" class="rimuoviPost" /></a>';
				
                $final .= '</div>';
            }

            return $final;
        }

        //inserisce un nuovo commento nel Db
        public function inserisciC() {
            $data = date ("Y-m-d G:i");
            $query='INSERT INTO comment VALUES ("","'.$_POST['area'].'","'.$data.'","'.$_SESSION['username'].'",'.$_GET['id'].')';
            $this->db->query($query);
            $this->db->execute($query);
        }
		
		//elimina un commento
		public function eliminaCommento($id) {
			$query = 'DELETE FROM comment WHERE comment_id = '.$id;
			$this->db->query($query);
			$this->db->execute();
			
			//il numero di commenti di un utente e del post si aggiornano con il trigger del database
		}

    }
?>
