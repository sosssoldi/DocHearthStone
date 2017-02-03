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
		
		if(isset($_POST['submit']) && isset($_FILES['fileToUpload']))
			if($_FILES['fileToUpload']['tmp_name'] != "")
				$content = $this->caricaImmagine($content);
			else
				$content = str_replace(':erroreImmagine:','<p class="errore">Inserire un\'immagine</p>',$content);
		else
			$content = str_replace(':erroreImmagine:','',$content);
		
		$rs = $this->getDati();
	
		$content = str_replace(":immagineProfilo:", ' '.$rs[0]['photo_id'], $content);
        $content = str_replace(":user:", ' '.$rs[0]['username'], $content);
        $content = str_replace(":mail:", ' '.$rs[0]['email'], $content);
        $content = str_replace(":data:", ' '.$rs[0]['entry_date'], $content);
        $content = str_replace(":nom:", ' '.$rs[0]['name'], $content);
        $content = str_replace(":cog:", ' '.$rs[0]['surname'], $content);
        $content = str_replace(":mieimazzi:", $this->mazzi(), $content);
        $content = str_replace(":mieguide:", $this->guide(), $content);

        echo $content;
    }

    public function getDati() {

        $query = 'SELECT photo_id, username, name, surname, email, entry_date FROM user ';
        $query .= 'WHERE username = "'.$_SESSION["username"].'"';

        $this->db->query($query);
        $rs = $this->db->resultset();

        return $rs;
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
            $riga .= '<td><img class="classe" src="images/icon/'.$row['image'].'.png"></td>';
			
			if($row['likes'] > 0)
				$riga .= '<td class="likespos">+'.$row['likes'].'</td>';
			
			if($row['likes'] < 0)
				$riga .= '<td class="likesneg">'.$row['likes'].'</td>';
			
			if($row['likes'] == 0)
				$riga .= '<td>'.$row['likes'].'</td>';
            
			$riga .= '<td><a href="eliminaMazzo.php"><img class="delete" src="images/icon/remove.png" alt="Elimina"></a></td>';
			$riga .= '</tr>';
        }

        return $riga;

    }

    public function guide(){

        $query = 'SELECT guide_id, title, hero.type as image ';
        $query .= 'FROM guide join hero on guide.hero_id = hero.hero_id ';
        $query .= 'WHERE user_name ="'.$_SESSION["username"].'"';

        $this->db->query($query);
        $rs = $this->db->resultset();

        $riga = '';

        foreach($rs as $row) {
            $riga .= '<tr>';
			$riga .= '<td><a href="mostraGuida.php?guida='.$row['guide_id'].'">'.$row['title'].'</a></td>';
            $riga .= '<td><img class="classe" src="images/icon/'.$row['image'].'.png"></td>';
			$riga .= '<td><a href="eliminaGuida.php"><img class="delete" src="images/icon/remove.png" alt="Elimina"></a></td>';
            $riga .= '</tr>';
        }

        return $riga;
    }

    public function footer() {
        echo file_get_contents("html/footer.html");
    }
	
	private function caricaImmagine($c) {		
		
		$status = 0;
		/*
		status = 0 -> immagine valida
		status = 1 -> non è un'immagine
		status = 2 -> dimensione massima dell'immagine superata
		status = 3 -> formato non supportato
		*/
		
		$target_dir = 'images/user/';
		$target_file = $target_dir.$_SESSION['username'].'.'.pathinfo($_FILES['fileToUpload']['name'],PATHINFO_EXTENSION);

		// Controllo se un'immagine è fake
		if(getimagesize($_FILES['fileToUpload']['tmp_name']) === false)
			$status = 1;
		
		// Controllo la dimensione del file
		if ($_FILES['fileToUpload']['size'] > 1000000)
			$status = 2;
		
		// Formato dell'immagine
		$imageType = pathinfo($_FILES['fileToUpload']['name'],PATHINFO_EXTENSION);
		if($imageType != "jpg" && $imageType != "png" && $imageType != "jpeg" && $imageType != "gif" )
			$status = 3;
		
		switch($status) {
			case 0:
				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
					//Aggiorno il link in cui si trova l'immagine
					$query = 'UPDATE user SET photo_id = "'.$target_file.'" WHERE username = "'.$_SESSION['username'].'"';
					$this->db->query($query);
					$this->db->execute();
					$c = str_replace(':erroreImmagine:','', $c);
				}
				else
					$c = str_replace(':erroreImmagine:', '<p class="errore">Errore nel caricare l\'immagine</p>', $c);
				break;
			case 1:
				$c = str_replace(':erroreImmagine:', '<p class="errore">Il file inserito non è un\'immagine</p>', $c);
				break;
			case 2:
				$c = str_replace(':erroreImmagine:', '<p class="errore">Il file inserito è troppo grande</p>', $c);
				break;
			case 3:
				$c = str_replace(':erroreImmagine:', '<p class="errore">Sono accettati solo file JPG, JPEG, PNG & GIF</p>', $c);
				break;
		}
		
		return $c;
	}
}
?>