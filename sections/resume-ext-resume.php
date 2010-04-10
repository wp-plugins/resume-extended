<?php
require_once('resume-ext-section.php');

include_once('resume-ext-general.php');
include_once('resume-ext-skills.php');
include_once('resume-ext-employment.php');
include_once('resume-ext-education.php');
include_once('resume-ext-awards.php');
include_once('resume-ext-finish.php');

class resume_ext_resume 
/*extends resume_ext_section 
implements resume_ext_exportable*/ {
	
	
	private $resume_section_lookup = Array(
	"general",
	"skills",
	"employment",
	"education",
	"awards",
	"finish"
	);
}
?>