<!DOCTYPE html>
<html lang="it">
	<head>
		<meta charset="utf-8" />
		<title>Forum - DocHearthStone</title>
		<meta name="title" content="Forum - DocHearthStone" />
		<meta name="description" content="Forum di discussione su tutto quello che riguarda il mondo di Hearthstone." />
		<meta name="keyword" content="Hearthstone, DocHearthStone, forum, carte, eroi, standard, selvaggio, rissa, avventure, mazzi" />
		<link rel="stylesheet" href="css/forum.css" type="text/css" />
        <link rel="stylesheet" href="css/header.css" type="text/css" />
        <link rel="stylesheet" href="css/generale.css" type="text/css" />
	</head>
	<body>
        <?php
            include_once 'autoloader.php';
            use \php\Page\Forum;

            $obj= new Forum();

            $obj->header();
            $obj->content();
            $obj->footer();
        ?>
    </body>
</html>
