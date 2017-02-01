<!DOCTYPE html>
<html lang="it">
	<head>
		<meta charset="utf-8" />
		<title>Mazzi - DocHearthStone</title>
		<meta name="title" content="Mazzi - DocHearthStone" />
		<meta name="description" content="Tutti i mazzi creati dai professionisiti e dagli utenti di DocHearthStone." />
		<meta name="keyword" content="Hearthstone, DocHearthStone, mazzo, aggro, control, midrange, combo, secret, freeze, face" />
		<link rel="stylesheet" href="css/mostraMazzo.css" type="text/css" />
        <link rel="stylesheet" href="css/header.css" type="text/css" />
        <link rel="stylesheet" href="css/generale.css" type="text/css" />
	</head>
	<body>
        <?php
            include_once 'autoloader.php';
            use \php\Page\showDeck;

            $obj=new showDeck();
            $obj->header();
            $obj->content();
            $obj->footer();
        ?>
    </body>
</html>
