<?php
    namespace php\Page;

    include_once "Page.php";
    use \php\Database\Database;
    use \php\Database\MySQLConnection\MySQLConnection;

    class Forum implements Page {

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
                        <input id="logoutButton" type="submit" value="Logout">
                    </form>'
                ,$head);
            }
            else
            {
                $head = str_replace(":login:",
                '<li lang="en"><a href="login.php">LOGIN</a></li>', $head);
				$head = str_replace(":utente:",'',$head);
            }

            //here
            $head = str_replace('<li><a href="forum.php">FORUM</a></li>',
            '<li class="here">FORUM</li>', $head);

            echo $head;
        }
        public function content() {
            $content=file_get_contents("html/forum.html");
            $rs=$this->lastTopic();
            $i=1;
            foreach ($rs as $row)
            {
                $content=str_replace(':l'.$i.':',$this->creatoreTopic($row['Id']),$content);
                $content=str_replace(':n'.$i.':',$row['N'],$content);
                $i++;
            }

            echo $content;
        }

        public function footer() {
            echo file_get_contents("html/footer.html");
        }

        //cerca per una determinata section l'utente che ha creato l'ultimo topic
        public function creatoreTopic($id) {
            $query='SELECT T.user_name as nome
                    FROM topic T
                    WHERE T.section_id='.$id.'
                    ORDER BY T.topic_id DESC LIMIT 1';

            $this->db->query($query);
            $rs = $this->db->resultset();

            if($this->db->rowCount()>0)
                return $rs[0]['nome'];
            else
                return null;
        }

        //ritorna tutti i topic presenti ordinati per id della sezione
        public function lastTopic() {
            $query='SELECT S.section_id as Id, S.name as Nome, S.num_thread as N
                    FROM section S
                    ORDER BY S.section_id';

            $this->db->query($query);
            $rs=$this->db->resultset();

            return $rs;
        }
    }
?>
