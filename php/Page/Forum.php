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

            //here
            $head = str_replace('<li><a href="forum.php">FORUM</a></li>',
            '<li class="here">FORUM</li>', $head);

            echo $head;
        }
        public function content() {
            $content=file_get_contents("html/forum.html");
            $rs=$this->lastTopic();

            /*$content=str_replace(':lGenerale:',$rs[0]['Nome'],$content);
            $content=str_replace(':nGenerale:',$rs[0]['N'],$content);
            $content=str_replace(':lArena:',$rs[1]['Nome'],$content);
            $content=str_replace(':nArena:',$rs[1]['N'],$content);
            $content=str_replace(':lRissa:',$rs[2]['Nome'],$content);
            $content=str_replace(':nRissa:',$rs[2]['N'],$content);
            $content=str_replace(':lAvventure:',$rs[3]['Nome'],$content);
            $content=str_replace(':nAvventure:',$rs[3]['N'],$content);
            $content=str_replace(':lFormatoStd:',$rs[4]['Nome'],$content);
            $content=str_replace(':nFormatoStd:',$rs[4]['N'],$content);
            $content=str_replace(':lFormatoWild:',$rs[5]['Nome'],$content);
            $content=str_replace(':nFormatoWild:',$rs[5]['N'],$content);
            $content=str_replace(':lCarte:',$rs[6]['Nome'],$content);
            $content=str_replace(':nCarte:',$rs[6]['N'],$content);
            $content=str_replace(':lMazzo:',$rs[7]['Nome'],$content);
            $content=str_replace(':nMazzo:',$rs[7]['N'],$content);
            $content=str_replace(':lCacciatore:',$rs[8]['Nome'],$content);
            $content=str_replace(':nCacciatore:',$rs[8]['N'],$content);
            $content=str_replace(':lDruido:',$rs[9]['Nome'],$content);
            $content=str_replace(':nDruido:',$rs[9]['N'],$content);
            $content=str_replace(':lGuerriero:',$rs[10]['Nome'],$content);
            $content=str_replace(':nGuerriero:',$rs[10]['N'],$content);
            $content=str_replace(':lLadro:',$rs[11]['Nome'],$content);
            $content=str_replace(':nLadro:',$rs[11]['N'],$content);
            $content=str_replace(':lMago:',$rs[12]['Nome'],$content);
            $content=str_replace(':nMago:',$rs[12]['N'],$content);
            $content=str_replace(':lPaladino:',$rs[13]['Nome'],$content);
            $content=str_replace(':nPaladino:',$rs[13]['N'],$content);
            $content=str_replace(':lPrete:',$rs[14]['Nome'],$content);
            $content=str_replace(':nPrete:',$rs[14]['N'],$content);
            $content=str_replace(':lSciamano:',$rs[15]['Nome'],$content);
            $content=str_replace(':nSciamano:',$rs[15]['N'],$content);
            $content=str_replace(':lStregone:',$rs[16]['Nome'],$content);
            $content=str_replace(':nStregone:',$rs[16]['N'],$content);*/

            echo $content;
        }

        public function footer() {
            echo file_get_contents("html/footer.html");
        }

        public function lastTopic() {
            $query='SELECT max(T.topic_id), T.title, T.user_name as Nome, S.section_id, S.num_thread as N
                    FROM topic T join section S on (T.section_id=S.section_id)
                    GROUP BY S.section_id
                    ORDER BY S.section_id';

            $this->db->query($query);
            $rs=$this->db->resultset($query);

            return $rs;
        }
    }
?>
