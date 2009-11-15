<?php
function resume_ext_pdf_fallback($section) {
	echo "<hr><pre>";
	var_dump($section);
	echo "</pre>";
}

function resume_ext_pdf_general($section) {
	echo "<hr><h1>General Section</h1><pre>";
	var_dump($section);
	echo "</pre>";
}
?>
