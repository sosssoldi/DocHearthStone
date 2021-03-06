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

        //here
        $head = str_replace('<li lang="en"><a href="index.php">HOMEPAGE</a></li>',
        '<li lang="en" class="here">HOMEPAGE</li>', $head);

        echo $head;
    }

    public function content() {
        $content=file_get_contents("html/index.html");

        $content = str_replace(":profilo:", $this->mostraProfilo(), $content);
        $content = str_replace(":tabelladeck:", $this->top10Deck(), $content);
        $content = str_replace(":tip:", $this->randomTip(), $content);
        $content = str_replace(":tabellaguide:", $this->top5Guides(), $content);

        echo $content;
    }
    public function footer() {
		echo file_get_contents("html/footer.html");
	}

    public function mostraProfilo() {
        if(isset($_SESSION["username"])) {
            $output = "<p>Ciao {$_SESSION['username']}</p>
            <form method=\"post\" action=\"user.php\"><input type=\"submit\" value=\"Profilo\"></form>";
        } else {
            $output = "<p>Entra anche tu a far parte di Doc HearthStone!</p>
    		<form method=\"post\" action=\"registrazione.php\"><input type=\"submit\" value=\"Registrati\" /></form>
    		<p>Sei gi&agrave; dei nostri?</p>
    		<form method=\"post\" action=\"login.php\"><input type=\"submit\" value=\"Login\" /></form>";
        }
        return $output;
    }

    public function top10Deck() {
        $query  = 'SELECT deck_id, hero.type as img, deck.name, likes FROM deck, hero ';
        $query .= 'WHERE deck.hero_id = hero.hero_id ';
        $query .= 'ORDER BY likes desc LIMIT 10;';
        $rs = $this->executeQuery($query);
        $output = "";
        foreach ($rs as $row) {
            $output .= "<tr><td headers=\"rots\" ><img class=\"classe\" src=\"images/icon/{$row['img']}.png\" alt=\"Icona eroe: {$row['img']}\" /></td>";
            $output .= "<td headers=\"intc\" ><a href=\"mostraMazzo.php?mazzo={$row['deck_id']}\">{$row['name']}</a></td>";

			if($row['likes'] > 0)
				$output .= "<td headers=\"rotd\" class=\"ratepos\">+{$row['likes']}</td></tr>";

			if($row['likes'] < 0)
				$output .= "<td headers=\"rotd\" class=\"rateneneg\">{$row['likes']}</td></tr>";

			if($row['likes'] == 0)
				$output .= "<td headers=\"rotd\" >{$row['likes']}</td></tr>";
        }
        return $output;
    }

    public function top5Guides() {
        $query = 'SELECT guide_id, title FROM guide ORDER BY guide_id desc LIMIT 5';
        $rs = $this->executeQuery($query);
        $output = "";
        foreach ($rs as $row) {
            $output .= "<li><a href=\"mostraGuida.php?guida={$row['guide_id']}\">{$row['title']}</a></li>";
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
