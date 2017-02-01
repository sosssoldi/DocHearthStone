<!DOCTYPE html>
<html lang="it">
	<head>
		<meta charset="utf-8" />
		<title>Guide - DocHearthStone</title>
		<meta name="title" content="Guide - DocHearthStone" />
		<meta name="description" content="Tutte le guide fatte dai migliori giocatori per imparare tutto quello che c'è da sapere sugli eroi." />
		<meta name="keyword" content="Hearthstone, DocHearthStone, guide, eroi, potere eroe, trucchi, consigli" />
		<link rel="stylesheet" href="css/guide.css" type="text/css" />
        <link rel="stylesheet" href="css/header.css" type="text/css"/>
		<link rel="stylesheet" href="css/generale.css" type="text/css"/>
	</head>
	<body>
        <?php
            include_once 'autoloader.php';
            use \php\Page\Guide;

            $obj = new Guide();
            $obj->header();
            $obj->content();
            $obj->footer();
        ?>
    </body>
</html>
