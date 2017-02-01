<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="utf-8" />
        <title>Accedi - DocHearthStone</title>
        <meta name="title" content="Registrati - Doc HearthStone" />
        <meta name="description" content="Registrati a Doc HearthStone" />
        <meta name="keyword" content="DocHearthStone, HearthStone" />
        <link rel="stylesheet" href="/css/login.css" type="text/css" />
        <link rel="stylesheet" href="css/header.css" type="text/css" />
        <link rel="stylesheet" href="css/generale.css" type="text/css" />
        <script type="text/javascript" src="/script/checkInputRegistrazione.js"></script>
    </head>
	<body>
        <?php
            include_once 'autoloader.php';
            use \php\Page\Registrazione;

            $obj = new Registrazione();
            $obj->header();
            $obj->content();
            $obj->footer();
        ?>
    </body>
</html>
