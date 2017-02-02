<?php
namespace php\Page;

include_once "Page.php";

use \php\Database\Database;
use \php\Database\MySQLConnection\MySQLConnection;

class Homepage implements Page {

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
        $head = str_replace('<li lang="en"><a href="index.php">HOMEPAGE</a></li>',
        '<li lang="en" class="here">HOMEPAGE</li>', $head);

        echo $head;
    }
    public function content() {
        $content=file_get_contents("html/index.html");

        $content = str_replace(":tabelladeck:", $this->top10Deck(), $content);
        $content = str_replace(":tip:", $this->randomTip(), $content);
        $content = str_replace(":tabellaguide:", $this->top5Guides(), $content);

        echo $content;
    }
    public function footer(){}
    public function top10Deck() {
        $query  = 'SELECT deck_id, hero_power.image as img, deck.name, likes FROM deck, hero, hero_power ';
        $query .= 'WHERE deck.hero_id = hero.hero_id AND hero.h_power = hero_power.hero_power_id ';
        $query .= 'ORDER BY likes desc LIMIT 10;';
        $rs = $this->executeQuery($query);
        $output = "";
        foreach ($rs as $row) {
            $output .= "<tr><td><img class=\"classe\" src=\"images/icon/{$row['img']}\"></img></td>";
            $output .= "<td>{$row['name']}</td>";
            $output .= "<td>{$row['likes']}</td></tr>";
        }
        return $output;
    }
    public function top5Guides() {
        $query = 'SELECT guide_id, title FROM guide ORDER BY valutation desc LIMIT 5';
        $rs = $this->executeQuery($query);
        $output = "";
        foreach ($rs as $row) {
            $output .= "<li>{$row['title']}</li>";
        }
        return $output;
    }
    public function randomTip() {
        $tid = rand(1,11);
        $query = "SELECT content FROM tip WHERE tip_id = {$tid}";
        $rs = $this->executeQuery($query);
        return $rs[0]["content"];
    }
    public function executeQuery($q) {
        $this->db->query($q);
        return $this->db->resultset();
    }
}
?>
