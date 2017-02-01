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
        }
        else {
            $head = str_replace(":login:",
            '<li lang="en"><a href="login.php">LOGIN</a></li>', $head);
        }

        //here
        $head = str_replace('<li lang="en"><a href="index.php">HOMEPAGE</a></li>',
        '<li lang="en" class="here">HOMEPAGE</li>', $head);

        echo $head;
    }
    public function content() {
        $content=file_get_contents("html/index.html");

        $content = str_replace(":tabelladeck:", $this->top10Deck(), $content);

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
        $query = 'SELECT guide_id, title FROM guide ORDER BY valutation LIMIT 5';
        $rs = executeQuery($query);
    }
    public function randomTip() {
        $bind = array(':tip_id' => rand(1, 50));
        $query = 'SELECT content FROM tips WHERE tip_id = :tip_id';
        $rs = executeQuery($query, $param, $value);
    }
    public function executeQuery($q, $bind = array()) {
        foreach ($bind as $k => $v) {
            $this->db->bind($k, $v);
        }
        $this->db->query($q);
        return $this->db->resultset();
    }

}
?>
