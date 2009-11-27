<?php
	$resume->add_format(1, "pdf/format.php", "PDF", "text/html");//"application/pdf");
		//$resume->add_section(1, "general", "resume_ext_pdf_general");
		$resume->add_section(1, "fallback", "resume_ext_pdf_fallback");
		
	$resume->add_format(2, "html-enhanced/format.php", "XHTML Enhanced", "text/html");
		$resume->add_section(2, "fallback", "resume_ext_xhtml_fallback");
		
	$resume->add_format(3, "xrl/format.php", "XML Resume Library", "application/xml");
		$resume->add_section(3, "fallback", "resume_ext_xrl_fallback");
?>
