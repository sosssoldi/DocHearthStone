<!DOCTYPE html>
<html lang="it">
	<head>
		<meta charset="utf-8" />
		<title>DocHearthStone</title>
		<meta name="title" content="Homepage - DocHearthStone" />
		<meta name="description" content="Il portale italiano di Heartdstone per restare sempre aggiornati su Mazzi, Carte e Guide." />
		<meta name="keyword" content="HearthStone, DocHearthStone, Carte, Mazzi, Avventure, Forum, Guide, Blizzard, Battlenet" />
		<link rel="stylesheet" href="../css/index.css" type="text/css" />
        <link rel="stylesheet" href="../css/header.css" type="text/css" />
        <link rel="stylesheet" href="../css/generale.css" type="text/css" />
	</head>
    <body>
        <?php
            include_once 'autoloader.php';
            use \php\Page\Homepage;

            $obj = new Homepage();

            $obj->header();
            $obj->content();
            $obj->footer();

        ?>
    </body>
</html>
