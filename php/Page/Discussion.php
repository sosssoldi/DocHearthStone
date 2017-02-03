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
            $content=file_get_contents("html/discussione.html");

            if (isset($_SESSION['username']))
                $content=str_replace(':commento:', '<form method="post" action="discussione.php?id=:idpagina:">
                			<label for="area">Inserisci un commento</label>
                			<textarea id="area" name="area"></textarea>
                			<input type="submit" value="COMMENTA" />
                		</form>',$content);
            else {
                $content=str_replace(':commento:','',$content);
            }

            if (isset($_POST['area']) && $_POST['area']!="")
                $this->inserisciC();

            $content=str_replace(':idpagina:',$_GET['id'],$content);

            $content=str_replace(':nomediscussione:',$this->getDiscussione(),$content);

            $content=str_replace(':posts:',$this->getC(),$content);

            echo $content;

        }

        public function footer() {
            echo file_get_contents("html/footer.html");
        }

        public function getDiscussione() {
            $query='SELECT T.title as Titolo
                    FROM topic T
                    WHERE T.topic_id="'.$_GET['id'].'"';

            $this->db->query($query);
            $rs = $this->db->resultset();

            return $rs[0]['Titolo'];
        }

        public function getC() {
            $query='SELECT U.username as Nome, U.entry_date as Data, U.count_post as N, U.photo_id as Foto, C.content as Commento, C.creation_date as DataC
                    FROM user U join comment C on (C.user_name=U.username) join topic T on (T.topic_id=C.topic_id)
                    WHERE T.topic_id='.$_GET['id'];

            $this->db->query($query);
            $rs = $this->db->resultset();

            $final="";

            foreach ($rs as $row) {
                $final.=
                '<div class="post">
                    <div class="dataora">
                        <p>'.$row['DataC'].'</p>
                    </div>
				    <div class="userptext">
                        <div class="user">
					        <img class="fotoProfilo" src="'.$row['Foto'].'" alt="Immagine profilo utente">
                            <p>'.$row['Nome'].'</p>
                            <p>Data di entrata: '.$row['Data'].'</p>
					        <p>Interventi nel forum: '.$row['N'].'</p>
                        </div>
                        <div class="text">
                            <p>'.$row['Commento'].'</p>
   		                </div>
				    </div>
                </div>';
            }

            return $final;
        }

        public function inserisciC() {
            $data = date ("Y-m-d G:i");
            $query='INSERT INTO comment VALUES ("","'.$_POST['area'].'","'.$data.'","'.$_SESSION['username'].'",'.$_GET['id'].')';
            $this->db->query($query);
            $this->db->execute($query);
        }

    }
?>
