<?php
namespace Classes\Page;

interface Page {
	public function header();
	public function content();
	public function footer();
}
?>
