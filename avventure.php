<!DOCTYPE html>
<html lang="it">
	<head>
		<meta charset="utf-8" />
		<title>Avventure - DocHearthStone</title>
		<meta name="title" content="Avventure - DocHearthStone" />
		<meta name="description" content="La descrizione delle carte e degli eroi che si troveranno acquistando ciascuna avventura." />
		<meta name="keyword" content="DocHearthStone, avventura, la maledizione di Naxxramas, naxxramas, Massiccio Roccianera, roccianera, Lega degli Esploratori, Esploratori, Una notte a Karazhan, Karazhan" />
    	<link rel="stylesheet" href="css/avventure.css" type="text/css" />
        <link rel="stylesheet" href="css/header.css" type="text/css" />
        <link rel="stylesheet" href="css/generale.css" type="text/css" />
		<script src="../script/avventure.js"></script>
	</head>
    <body onload="init();">
    <?php
        include_once 'autoloader.php';
        use \php\Page\Adventure;

        $obj= new Adventure();

        $obj->header();
        $obj->content();
        $obj->footer();
    ?>
    </body>
</html>