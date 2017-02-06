<?php
	namespace php\Page;

	include_once "Page.php";

	use \php\Database\Database;
	use \php\Database\MySQLConnection\MySQLConnection;

	class createDeck implements Page {

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
	                    <input id="logoutButton" type="submit" value="Logout">
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

			$content=file_get_contents("html/creaMazzo.html");

			$content=str_replace(':eroePagina:',$_GET['eroe'],$content);

			$content=str_replace(':listaCarte:',$this->carteDisp(),$content);

			$count=$this->contaCarte();

			//controllo il numero di carte selezionate per la creazione del mazzo (devono essere 30)
			if (!isset($_POST['submit']))
				$content=str_replace(':errore:','',$content);
			else
			{
				if ($count<30) {
					$content=str_replace(':errore:','<p class="errore">Hai selezionato '.$count.' carte (devono essere 30).</p>',$content);
					$content = $this->aggiornaLabel($content);
				}
				else
					if ($count>30) {
						$content=str_replace(':errore:','<p class="errore">Hai selezionato '.$count.' carte (devono essere 30).</p>',$content);
						$content = $this->aggiornaLabel($content);
					}
					else
					{
						$content=str_replace(':errore:','',$content);
						$this->insertDb();
					}
			}

			echo $content;
		}

		public function footer() {
			echo file_get_contents("html/footer.html");
		}

		//ritorna tutte le possibili carte da aggiungere al mazzo
		public function queryCarte() {
			$query='SELECT C.name as Nome, C.rarity as R, C.mana as costo, C.card_id as Id
					FROM card C join hero_card HC on (C.card_id=HC.card_id) join hero H on (HC.hero_id=H.hero_id)
					WHERE H.type="'.$_GET['eroe'].'"
					ORDER BY costo, Nome';

			$this->db->query($query);
			$rs = $this->db->resultset();

			return $rs;
		}

		//crea il codice HTML in base alle carte disponibili
		public function carteDisp()
		{
			$rs=$this->queryCarte();
			$final="";
			$i=1;
			foreach ($rs as $row) {
				if($row['R']=="Leggendaria")
				{
					if (($i%2)==1)
					{
						$final.='<div class="carte1">
									<span class="costo">'.$row['costo'].'</span>
									<span class="nome '.$row['R'].'" onmouseover="showImg(this, \''.$row["Id"].'\');" onmouseout="hideImg(this);">'.$row['Nome'].'</span>
	    							<fieldset>
										<label for="quantita1'.$i.'">1</label>
								        <input type="radio" class="radio" id="quantita1'.$i.'" name="quantita1'.$i.'" value="1"/>
										<label for="quantita2'.$i.'">2</label>
								        <input type="radio" class="radio" id="quantita2'.$i.'" name="quantita1'.$i.'" value="2" disabled="disabled"/>
								    </fieldset>
								</div>';
					}
					else
					{
						$final.='
							<div class="carte2">
								<span class="costo">'.$row['costo'].'</span>
								<span class="nome '.$row['R'].'" onmouseover="showImg(this, \''.$row["Id"].'\');" onmouseout="hideImg(this);">'.$row['Nome'].'</span>
									<fieldset>
									<label for="quantita1'.$i.'">1</label>
									<input type="radio" class="radio" id="quantita1'.$i.'" name="quantita2'.$i.'" value="1"/>
									<label for="quantita2'.$i.'">2</label>
									<input type="radio" class="radio" id="quantita2'.$i.'" name="quantita2'.$i.'" value="2" disabled="disabled"/>
									</fieldset>
							</div>';
					}
				}
				else
				{
					if (($i%2)==1)
					{
						$final.='<div class="carte1">
									<span class="costo">'.$row['costo'].'</span>
									<span class="nome '.$row['R'].'" onmouseover="showImg(this, \''.$row["Id"].'\');" onmouseout="hideImg(this);">'.$row['Nome'].'</span>
		    						<fieldset>
									<label for="quantita1'.$i.'">1</label>
									<input type="radio" class="radio" id="quantita1'.$i.'" name="quantita1'.$i.'" value="1"/>
									<label for="quantita2'.$i.'">2</label>
									<input type="radio" class="radio" id="quantita2'.$i.'" name="quantita1'.$i.'" value="2"/>
									</fieldset>
								</div>';
					}
					else
					{
						$final.='
							<div class="carte2">
								<span class="costo">'.$row['costo'].'</span>
								<span class="nome '.$row['R'].'" onmouseover="showImg(this, \''.$row["Id"].'\');" onmouseout="hideImg(this);">'.$row['Nome'].'</span>
								<fieldset>
									<label for="quantita1'.$i.'">1</label>
									<input type="radio" class="radio" id="quantita1'.$i.'" name="quantita2'.$i.'" value="1"/>
									<label for="quantita2'.$i.'">2</label>
									<input type="radio" class="radio" id="quantita2'.$i.'" name="quantita2'.$i.'" value="2"/>
								</fieldset>
							</div>';
					}
				}
				$i++;
			}
			return $final;
		}

		//ritorna il numero di carte selezionate per l'aggiunta al mazzo
		public function contaCarte() {
			$rs=$this->queryCarte();
			$n=$this->db->rowCount();

			$count=0;
			for ($i = 1; $i <= $n; $i++)
			{
				if (($i%2)==1)
				{
					if (isset($_POST['quantita1'.$i]))
					{
						if($_POST['quantita1'.$i] == "1")
							$count=$count+1;
						else
							if($_POST['quantita1'.$i] == "2")
								$count=$count+2;
					}
				}
				else
				{
					if (isset($_POST['quantita2'.$i]))
					{
						if($_POST['quantita2'.$i] == "1")
							$count=$count+1;
						else
							if($_POST['quantita2'.$i] == "2")
								$count=$count+2;
					}
				}
			}

			return $count;

		}

		//ritorna l'id dell'eroe corrispondente alla query string
		public function gethId(){
			$query='SELECT H.hero_id as Id
					FROM hero H
					WHERE H.type="'.$_GET['eroe'].'"';

			$this->db->query($query);
			$rs = $this->db->resultset();

			return $rs[0]['Id'];
		}

		//ritorna l'id del mazzo appena creato per poi aggiungere le carte associate
		public function getdId()
		{
			$query='SELECT D.deck_id as Id
					FROM deck D
					WHERE D.name="'.$_POST['nome'].'"';

			$this->db->query($query);
			$rs = $this->db->resultset();

			return $rs[0]['Id'];
		}

		//inserisce il nuovo mazzo in deck e le carte in deck_card
		public function insertDb()
		{
			$data = date ("Y-m-d G:i");
			$id=$this->gethId();
			if (isset($_POST['nome']) && $_POST['nome']!="")
			{
				$query='INSERT INTO deck VALUES ("","'.$_POST['nome'].'","'.$_POST['Commento'].'",0,"'.$data.'","'.$id.'","'.$_SESSION['username'].'")';
				$this->db->query($query);
				$this->db->execute($query);

				$rs=$this->queryCarte();
				$n=$this->db->rowCount();

				$deck=$this->getdId();

				$query="";
				for ($i = 1; $i <= $n; $i++)
				{
					if (($i%2)==1)
					{
						if (isset($_POST['quantita1'.$i]))
						{
							$query='INSERT INTO card_deck VALUES ("","'.$deck.'","'.$rs[$i-1]['Id'].'")';
							if($_POST['quantita1'.$i] == "2")
							{
								$this->db->query($query);
								$this->db->execute($query);
							}
							$this->db->query($query);
							$this->db->execute($query);
						}
					}
					else
					{
						if (isset($_POST['quantita2'.$i]))
						{
							$query='INSERT INTO card_deck VALUES ("","'.$deck.'","'.$rs[$i-1]['Id'].'")';
							if($_POST['quantita2'.$i] == "2")
							{
								$this->db->query($query);
								$this->db->execute($query);
							}
							$this->db->query($query);
							$this->db->execute($query);
						}

					}
				}
			}
			header("Location: user.php");
		}

		//aggiorna la label in base ai dati che sono stati inseriti in precedenza
		private function aggiornaLabel($c) {

			$c = str_replace('<input type="text" id="nome" required autocomplete="off" name="nome" class="stringa"/>','<input type="text" id="nome" required autocomplete="off" name="nome" class="stringa" value="'.$_POST['nome'].'"/>',$c);
			$c = str_replace('<textarea id="area" name="Commento"></textarea>','<textarea id="area" name="Commento">'.$_POST['Commento'].'</textarea>',$c);
			
			$n = count($this->queryCarte());

			$count=0;
			
			//aggiorno i radioButton
			for ($i = 1; $i <= $n; $i++) {
				if (($i%2)==1) {
					if (isset($_POST['quantita1'.$i])) {
						if($_POST['quantita1'.$i] == "1")
							$c = str_replace('<input type="radio" class="radio" id="quantita1'.$i.'" name="quantita1'.$i.'" value="1"/>',
							'<input type="radio" class="radio" id="quantita1'.$i.'" name="quantita1'.$i.'" value="1" checked="checked"/>',$c);
						else
							if($_POST['quantita1'.$i] == "2")
								$c = str_replace('<input type="radio" class="radio" id="quantita2'.$i.'" name="quantita1'.$i.'" value="2"/>',
								'<input type="radio" class="radio" id="quantita2'.$i.'" name="quantita1'.$i.'" value="2" checked="checked"/>',$c);
					}
				}
				else {
					if (isset($_POST['quantita2'.$i])) {
						if($_POST['quantita2'.$i] == "1")
							$c = str_replace('<input type="radio" class="radio" id="quantita1'.$i.'" name="quantita2'.$i.'" value="1"/>',
							'<input type="radio" class="radio" id="quantita1'.$i.'" name="quantita2'.$i.'" value="1" checked="checked"/>',$c);
						else
							if($_POST['quantita2'.$i] == "2")
								$c = str_replace('<input type="radio" class="radio" id="quantita2'.$i.'" name="quantita2'.$i.'" value="2"/>',
								'<input type="radio" class="radio" id="quantita2'.$i.'" name="quantita2'.$i.'" value="2" checked="checked"/>',$c);
					}
				}
			}
			
			return $c;
		}
	}
?>
