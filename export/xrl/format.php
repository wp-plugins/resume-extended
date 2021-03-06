<?php

function resume_ext_xrl_date($date) {
	$date = new DateTime("@$date") ?>
		<dayOfMonth><?php echo $date->format('j') ?></dayOfMonth>
		<month><?php echo $date->format('n') ?></month>
		<year><?php echo $date->format('Y') ?></year>
	<?php
}

function resume_ext_xrl_fallback($title, $section) {
	echo "<!--\n";
	echo $title . "\n";
	var_dump($section);
	echo "\n-->";
}

function resume_ext_xrl_general($title, $section) {
	echo <<< XRL_PROLOG
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE resume PUBLIC "-//Sean Kelly//DTD Resume 1.5.1//EN"
  "http://xmlresume.sourceforge.net/dtd/resume.dtd">
XRL_PROLOG;

	echo "<!--";
	var_dump($section);
	echo "-->";
	?>
	<resume>
		<header>
			<name>
				<?php echo $section['resume_name'] ?>
			</name>
			<address>
				<?php echo $section['resume_address'] ?>
			</address>
			<contact>
				<!--<phone></phone>-->
				<email><?php echo $section['resume_email'] ?></email>
				<url><?php echo $section['resume_website'] ?></url>
			</contact>
		</header>
		<objective>
			<?php echo $section['resume_objective'] ?>
		</objective>
<?php
}

function resume_ext_xrl_skills($title, $section) {	
	?>
		<skillarea>
			<title><?php echo $title ?></title><?php
	foreach($section as $sect) {?>
		
			<skillset>
				<title><?php echo $sect['resume_skillset_name'] ?></title><?php
				
		foreach($sect['resume_skills'] as $skill) {?>
				<skill><?php echo $skill ?></skill>
<?php
		}
?>
			</skillset>
<?php
	}	?>
		</skillarea>
	<?php
}

function resume_ext_xrl_employment($title, $section) {

	echo "<!--";
	var_dump($section);
	echo "-->";
	
	echo "<history>";
	
	foreach($section as $sect) { ?>
		<job>
			<jobtitle><?php echo $sect['resume_job_title'] ?></jobtitle>
			<employer><?php echo $sect['resume_employer'] ?></employer>
			<period>
				<from>
					<?php resume_ext_xrl_date($sect['resume_start_employ_timestamp']) ?>
				</from>
				<to><?php
		if($sect["resume_currently_employ"]){
			echo "<present />";
		} else {
			resume_ext_xrl_date($sect['resume_end_employ_timestamp']);
		}?>
					
				</to>
			</period>
			<description><?php echo $sect["resume_job_desc"] ?></description>
			<!--<projects>
				<project title=""></project>
			</projects>-->
		</job>
<?php
	}
	
	echo "</history>";
}

function resume_ext_xrl_education($title, $section) {

	echo "<!--";
	var_dump($section);
	echo "-->";
	
	echo "<academics><degree>";
	
	foreach($section as $sect) { ?>
		<degree>
			<institution><?php echo $sect['resume_institution'] ?></institution>
			<level><?php echo $sect['resume_degree'] ?></level>
			<major><?php echo $sect['resume_major'] ?></major> <?php
			if($sect['resume_major']) {	
				echo "<minor>", $sect['resume_minor'], "</minor>";
			} ?>
			<date><?php
		if($sect["resume_currently_enrolled"]){
			echo "<present />";
		} else {
			resume_ext_xrl_date($sect['resume_date_graduated_timestamp']);
		}?>
			</date>
			<!--<projects>
				<project title=""></project>
			</projects>-->
		</degree><?php
	}
	
	echo "</degree></academics>";
}

function resume_ext_xrl_awards($title, $section) {

	echo "<!--";
	var_dump($section);
	echo "-->";
	
	echo "<awards>";
	
	echo "<title>", $title ,"</title>";
	foreach($section as $sect) { ?>
		<award>
			<title><?php echo $sect['resume_award_title'] ?></title>
			<date><?php
			resume_ext_xrl_date($sect['resume_award_date_timestamp']);?>
			</date>
			<description>
			<?php echo $sect['resume_award_desc'] ?>
			</description>

			<!--<projects>
				<project title=""></project>
			</projects>-->
		</award><?php
	}
	
	echo "</awards>";
}

function resume_ext_xrl_finish($title, $section) {
	echo "</resume>";
}
?>
