<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title>Doc HearthStone</title>
		<meta name="title" content=""/>
		<meta name="description" content=""/>
		<meta name="keyword" content=""/>
		<link rel="stylesheet" href="css/generale.css" type="text/css"/>
		<link rel="stylesheet" href="css/header.css" type="text/css"/>
		<link rel="stylesheet" href="css/carte.css" type="text/css"/>
	</head>
	<body>
		<?php
			include_once 'autoloader.php';
			use \php\Page\Card;
			
			$obj = new Card();
			$obj->header();
			$obj->content();
			$obj->footer();
		?>
	</body>
<html>