<?php
namespace php\Page;

include_once "Page.php";

use \php\Database\Database;
use \php\Database\MySQLConnection\MySQLConnection;

class User implements Page {

    private $db = null;

    public function __construct() {
        $this->db = new Database(new MySQLConnection);
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

        //here
        $head = str_replace('<li><a href="user.php">'.$_SESSION["username"].'</a></li>',
        '<li lang="en" class="here">'.$_SESSION["username"].'</li>', $head);

        echo $head;
    }

    public function content() {
        $content=file_get_contents("html/utente.html");

        $content = str_replace(":user:", $this->getDati("username"), $content);
        $content = str_replace(":mail:", $this->getDati("email"), $content);
        $content = str_replace(":data:",$this->getDati("entry_date"), $content);
        $content = str_replace(":nom:", $this->getDati("name"), $content);
        $content = str_replace(":cog:", $this->getDati("surname"), $content);
        $content = str_replace(":mieimazzi:", $this->mazzi(), $content);
        $content = str_replace(":mieguide:", $this->guide(), $content);

        echo $content;
    }

    public function getDati($i) {

        $query = 'SELECT username, name, surname, email, entry_date FROM user ';
        $query .= 'WHERE username = "'.$_SESSION["username"].'"';

        $this->db->query($query);
        $rs = $this->db->resultset();

        return " ".$rs [0][$i];

    }

    public function mazzi(){

        $query = 'SELECT deck_id, deck.name as d, likes, hero.type as image ';
        $query .= 'FROM deck join hero on deck.hero_id = hero.hero_id ';
        $query .= 'WHERE user_name ="'.$_SESSION["username"].'"';

        $this->db->query($query);
        $rs = $this->db->resultset();

        $riga = '';

        foreach($rs as $row) {
            $riga .= '<tr>';
			$riga .= '<td><a href="mostraMazzo.php?mazzo='.$row['deck_id'].'">'.$row['d'].'</a></td>';
            $riga .= '<td><img class="classe" src="/images/icon/'.$row['image'].'.png"></td>';
			$riga .= '<td>'.$row['likes'].'</td>';
            $riga .= '</tr>';
        }

        return $riga;

    }

    public function guide(){

        $query = 'SELECT guide_id, title, valutation, hero.type as image ';
        $query .= 'FROM guide join hero on guide.hero_id = hero.hero_id ';
        $query .= 'WHERE user_name ="'.$_SESSION["username"].'"';

        $this->db->query($query);
        $rs = $this->db->resultset();

        $riga = '';

        foreach($rs as $row) {
            $riga .= '<tr>';
			$riga .= '<td><a href="mostraGuida.php?guida='.$row['guide_id'].'">'.$row['title'].'</a></td>';
            $riga .= '<td><img class="classe" src="/images/icon/'.$row['image'].'.png"></td>';
            $riga .= '<td>'.$row['valutation'].'</td>';
            $riga .= '</tr>';
        }

        return $riga;
    }

    public function footer() {
        echo file_get_contents("html/footer.html");
    }
}
?>
