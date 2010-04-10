<?php

function resume_ext_hresume_date($date_pretty, $timestamp, $class) {
	?>
	<abbr class="<?php echo $class ?>" title="<?php echo date('Y-m-d', $timestamp) ?>"><?php echo $date_pretty ?></abbr>
	<?php
}

function resume_ext_xhtml_fallback($title, $section) {
	echo "<!-- FALLBACK:\n";
	echo $title . "\n";
	var_dump($section);
	echo "\n-->";
	
	?><h2><?php echo $title ?></h2>
	<ul>
<?php
	foreach($section as $sect) {
		?>
		<li>
			<div class="title"><strong><?php echo $sect["strong"] ?></strong> <?php echo $sect["title"] ?></div>
			<div class="description"><?php echo $sect["desc"] ?></div>
		</li>
		<?php
	}
?>
	</ul>
<?php
}

function resume_ext_wp_general($title, $section) {
	?>
			<div class="hresume">
				<div class="contact vcard">
					<h1 class="fn n"  id="name"><?php echo $section['resume_name'] ?></h1>
					<span class="adr">
						<?php echo nl2br($section['resume_address']) ?>
					</span>
					<a href="mailto:<?php echo $section['resume_email'] ?>" class="email"><?php echo $section['resume_email'] ?></a>
					<a href="<?php echo $section['resume_website'] ?>" class="url"><?php echo $section['resume_website'] ?></a>
				</div>
				<div class="summary">
					<?php echo $section['resume_objective'] ?>
				</div>
<?php
}


function resume_ext_xhtml_general($title, $section) {
	echo <<< XHTML_PROLOG
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
XHTML_PROLOG;

	echo "<!--";
	var_dump($section);
	echo "-->";
	?>
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<title><?php echo $section['resume_name'] ?></title>
		</head>
<?php
	resume_ext_wp_general($title, $section);
}

function resume_ext_xhtml_skills($title, $section) {	
	?>
		<h2><?php echo $title ?></h2><?php
	foreach($section as $sect) {?>
			<h3><?php echo $sect['resume_skillset_name'] ?></h3>
			<ul>
				<?php
				
		foreach($sect['resume_skills'] as $skill) {?>
				<li><a href="http://www.wikipedia.com/wiki/<?php echo $skill ?>" rel="tag"><?php echo $skill ?></a></li>
<?php
		}
?>
			</ul>
<?php
	}	?>
	<?php
}

function resume_ext_xhtml_employment($title, $section) {

	echo "<!--";
	var_dump($section);
	echo "-->";
	
	?><h2><?php echo $title ?></h2><?php
	
	echo "<ol class=\"vcalendar\">";
	
	foreach($section as $sect) {?>
		<li class="vevent vcard experience">
			<object class="fn include" data="#name"></object>
			<span class="title"><?php echo $sect['resume_job_title'] ?></span>
			<span class="org"><?php echo $sect['resume_employer'] ?></span>
			<span class="period">
				<?php resume_ext_hresume_date($sect['resume_start_employ'], $sect['resume_start_employ_timestamp'], "dtstart") ?>
				&ndash;
				<?php
		if($sect["resume_currently_employ"]){
			resume_ext_hresume_date("present", time(), "dtend");
		} else {
			resume_ext_hresume_date($sect['resume_end_employ'], $sect['resume_end_employ_timestamp'], "dtend");
		}?>
			</span>
			<p class="description"><?php echo $sect["resume_job_desc"] ?></p>
			<!--<projects>
				<project title=""></project>
			</projects>-->
		</li>
<?php
	}
	
	echo "</ol>";
}

function resume_ext_xhtml_education($title, $section) {

	echo "<!--";
	var_dump($section);
	echo "-->";	
	
	?><h2><?php echo $title ?></h2><?php
	
	echo "<ol class=\"vcalendar\">";
	
	foreach($section as $sect) { ?>
		<li class="vevent vcard education">
			<span class="fn n org"><?php echo $sect['resume_institution'] ?></span>
			<span class="summary"><?php echo $sect['resume_degree'] ?> in <?php echo $sect['resume_major'] ?><?php
			if($sect['resume_major']) {	
				echo " and ", $sect['resume_minor'];
			} ?>
			</span>
			<span  class="period"><?php
		if($sect["resume_currently_enrolled"]){
			resume_ext_hresume_date("currently enrolled", time(), "dtend");
		} else {
			resume_ext_hresume_date($sect['resume_date_graduated'], $sect['resume_date_graduated_timestamp'], "dtend");
		}?>
			</span>
			<!--<projects>
				<project title=""></project>
			</projects>-->
		</li><?php
	}
	
	echo "</ol>";
}

function resume_ext_xhtml_awards($title, $section) {

	echo "<!--";
	var_dump($section);
	echo "-->";
	
	?><h2><?php echo $title ?></h2><?php
	
	echo "<ul class=\"vcalendar\">";

	foreach($section as $sect) { ?>
		<li class="vevent -rex-awards">
			<span class="summary"><?php echo $sect['resume_award_title'] ?></span>
			<span class="period"><?php
			resume_ext_hresume_date($sect['resume_award_date'], $sect['resume_award_date_timestamp'], "dtstart");?>
			</span>
			<span class="description">
			<?php echo $sect['resume_award_desc'] ?>
			</span>

			<!--<projects>
				<project title=""></project>
			</projects>-->
		</li><?php
	}
	
	echo "</ul>";
}

function resume_ext_wp_finish($title, $section) {?>
		</div>
<?php
}

function resume_ext_xhtml_finish($title, $section) {
	resume_ext_wp_finish($title, $section);
?>
	</body>
</html>
<?php
}
?>
