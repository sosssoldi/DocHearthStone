<?php
    namespace php\Page;

    include_once "Page.php";
    use php\Database\Database;
    use php\Database\MySQLConnection\MySQLConnection;

    class Login implements Page {

        private $db = null;
        private $status = 0;
        /*
        status = 0 -> nessun tentativo di login
        status = 1 -> errore, campo user vuoto
        status = 2 -> errore, campo pw vuoto
        status = 3 -> errore, campo user e pw vuoti
        status = 4 -> tentativo login valido, dati accettabili
        status = 5 -> login fallito*/

        public function __construct() {
            $this->db = new Database(new MysqlConnection());
        }

        public function header() {

            $this->controlloLogin();
            if ($this->status == 4) $this->provoLogin();

            $head = file_get_contents("html/header.html");
			$head = str_replace(':utente:', '', $head);

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
            $head = str_replace('<li><a href="guide.php">LOGIN</a></li>',
            '<li class="here">LOGIN</li>', $head);

            echo $head;
        }
        public function content() {
            $content=file_get_contents("html/login.html");
            $content = $this->cambiaLabel($content);

            echo $content;
        }
        public function footer() {
			echo file_get_contents("html/footer.html");
        }

        private function controlloLogin() {
            if ((isset($_POST["user"]) && ($_POST["user"])!="")&&
                (isset($_POST["password"]) && ($_POST["password"])!=""))
                    $this->status = 4;

            else if ((isset($_POST["user"]) && ($_POST["user"])!=""))
                    $this->status = 2;

            else if(isset($_POST["password"]) && ($_POST["password"])!="")
                    $this->status = 1;
            else if ((isset($_POST["user"]) && ($_POST["user"])=="")&&
                (isset($_POST["password"]) && ($_POST["password"])==""))
                    $this->status = 3;

        }

        private function cambiaLabel($c) {
            switch($this->status) {
                case 0:
                    $c = str_replace(':status:','',$c);
                    break;
                case 1:
                    $c = str_replace(':status:','<p>Errore inserimento username.</p>',$c);
                    break;
                case 2:
                    $c = str_replace(':status:','<p>Errore inserimento password.</p>',$c);
                    break;
                case 3:
                    $c = str_replace(':status:','<p>Errore inserimento username e password.</p>',$c);
                    break;
                case 5:
                    $c = str_replace(':status:','<p>Usename e/o password errati.</p>',$c);
                    break;
            }
            return $c;
        }

        private function provoLogin(){
            $pw = hash("sha256",$_POST["password"]);

            $query = "SELECT username, name, surname, entry_date, photo_id FROM user
            WHERE username = '{$_POST["user"]}' and
            password = '{$pw}'";

            $this->db->query($query);
            $rs = $this->db->resultset();

            if($this->db->rowCount() == 0){
                $this->status = 5;
            }
            else {
                $_SESSION['username'] = $rs[0]['username'];
                $_SESSION['name'] = $rs[0]['name'];
                $_SESSION['surname'] = $rs[0]['surname'];
                $_SESSION['entry_date'] = $rs[0]['entry_date'];
                $_SESSION['photo_id'] = $rs[0]['photo_id'];
                header("Location: index.php");
            }
        }
    }
?>
