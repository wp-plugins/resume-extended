<?php

/*  Copyright 2009 Aaron Spaulding  (email : Aaron@Sachimp.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA


Plugin Name: Resume
Plugin URI: http://sachimp/
Description: Create and manage your Resume all from your blog.
Version: 0.1
Author: Aaron Spaulding
Author URI: http://sachimp.com/
*/

/**
 * Determine the location
 */
$resume_path = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/';
$resume_ajax = get_bloginfo('wpurl') . "/wp-admin/admin-ajax.php";

$resume_pages = Array(
	'general' => "form_resume_general.php",
	'skills' => "form_resume_skills.php",
	'employment' => "form_resume_employment.php",
	'education' => "form_resume_education.php",
	'awards' => "form_resume_awards.php",
	'finish' => "form_resume_finish.php"
);

$resume_titles = Array(
	'general' => "General Info",
	'skills' => "Skills",
	'employment' => "Employment History",
	'education' => "Education",
	'awards' => "Awards &amp; Honors",
	'finish' => "Finish"
);

function resume_start_form($title) {
	global $resume_titles;
	global $resume_ajax;
 ?>
	<div class="sub_form">
	<!--<h3>New Resume - <?= $resume_titles[$title]?></h3>-->
	<div id="resume_<?= $title ?>_target">
	</div>
	<form action="<?= $resume_ajax ?>" method="post" id="resume_<?= $title ?>">
		<input type="hidden" name="action" value="resume_new" />
		<input type="hidden" name="sub_action" value="<?= $title ?>" />
<? }

function resume_end_form($next, $middle, $prev = NULL) {
	global $resume_titles; ?>
		<script type="text/javascript">(function($) {

			$(document).ready(function () {
				$("#next_page_<?= $next ?>").click(function() {
					$("#tabs").tabs('select', '#tab-<?= $next ?>');
				});
	<?	if($prev) { ?>
				$("#prev_page_<?= $prev ?>").click(function() {
					$("#tabs").tabs('select', '#tab-<?= $prev ?>');
				});
	<? } ?>
			});

		})(jQuery);</script>
		<p class="submit">
	<? if($prev) { ?>
			<input type="button" id="prev_page_<?= $prev ?>" value="&laquo;"/>
	<? } ?>
			<input type="submit" id="ajax_action" value="<?= $middle ?>" />
			<input type="button" id="next_page_<?= $next ?>" class="button-primary" value="<?= $resume_titles[$next]?> &raquo;"/>
		</p>
	</form>

	</div>
<? }

add_filter('admin_menu', 'resume_menu');
add_action('admin_print_styles', 'resume_admin_styles');
add_action('admin_print_scripts', 'resume_admin_scripts');

function resume_admin_styles () {
	global $resume_path;
	wp_enqueue_style('resume_admin_css', $resume_path . "admin_styles.css");
	//wp_enqueue_style('resume_ui_lightness', $resume_path . "css/dot-luv/jquery-ui-1.7.2.custom.css");
	//wp_enqueue_style('resume_ui_lightness', $resume_path . "css/ui-lightness/jquery-ui-1.7.2.custom.css");
	//wp_enqueue_style('resume_ui_lightness', $resume_path . "css/dark-hive/jquery-ui-1.7.2.custom.css");
	wp_enqueue_style('resume_ui_smoothness', $resume_path . "css/smoothness/jquery-ui-1.7.2.custom.css");
}

function resume_admin_scripts () {
	global $resume_path;
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core',false,array('jquery'));
	wp_enqueue_script('jquery-ui-tabs',false,array('jquery', 'jquery-ui-core'));
	wp_enqueue_script('jquery-form',false,array('jquery'));

	if(!wp_script_is('jquery-ui-datepicker')) { // The jquery datepicker might be eventually included.
		wp_enqueue_script('jquery-ui-datepicker', $resume_path . "ui.datepicker.js", array('jquery', 'jquery-ui-core'));
	} else {
		wp_enqueue_script('jquery-ui-datepicker', false, array('jquery', 'jquery-ui-core'));
	}

	wp_enqueue_script('resume-admin-js', $resume_path . "admin_scripts.js", array('jquery', 'jquery-ui-core', 'jquery-ui-tabs','jquery-ui-datepicker', 'jquery-form'));
}

function resume_menu()  {

	add_menu_page('New Resume', 'Resume', 8, __FILE__, 'resume_new_page');
	add_submenu_page(__FILE__, 'New Resume', 'New Resume', 8, __FILE__, 'resume_new_page');

	add_options_page('Resume Options', 'Resume', 8, 'resumeoptions', 'resume_options');
}

function resume_options() { ?>
<div class="wrap">
	<h2>Resume Options</h2>
</div>
<? }

function resume_new_page() {
	global $resume_pages;
	global $resume_titles;
	global $resume_ajax;

	$do_next = filter_input(INPUT_POST, 'do_next', FILTER_SANITIZE_STRING);

?>
	<div class="wrap resume_wrap">
	<h2>New R&eacute;sum&eacute;</h2>
	<div id="tabs">

	<ul class="tab_labels">
<?
	foreach($resume_titles as $key => $title) {
?>
		<li><a href="#tab-<?= $key ?>"><?= $title ?></a></li>
<?
	}
?>	</ul><?

	foreach($resume_pages as $key => $next) {
?>		<div id="tab-<?= $key ?>"> <?
		include($next);
?>		</div><?
	}
?>

	</div>
	</div>
<?
}

add_action('wp_ajax_resume_new', 'resume_new');
add_action('wp_ajax_resume_finalize', 'resume_finalize');
add_action('wp_ajax_resume_reset', 'resume_reset');


$resume_filters = Array(
	'general' => Array(
		'resume_title' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_objective' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_name' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_address' => FILTER_SANITIZE_MAGIC_QUOTES,
		'resume_email' => FILTER_VALIDATE_EMAIL,
		'resume_website' => FILTER_VALIDATE_URL),
	'skills' => Array(
		'resume_skillset_name' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_skillset_list' => FILTER_SANITIZE_SPECIAL_CHARS),
	'employment' => Array(
		'resume_employer' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_job_title' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_start_employ' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_end_employ' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_currently_employ' => FILTER_VALIDATE_BOOLEAN,
		'resume_job_desc' => FILTER_SANITIZE_SPECIAL_CHARS),
	'education' => Array(
		'resume_institution' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_major' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_minor' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_degree' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_currently_enrolled' => FILTER_VALIDATE_BOOLEAN,
		'resume_date_graduated' => FILTER_SANITIZE_SPECIAL_CHARS),
	'awards' => Array(
		'resume_award_title' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_award_date' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_award_desc' => FILTER_SANITIZE_SPECIAL_CHARS)
);

function resume_format_dl($items_array, $sub_action) {
	$output = "<dl>";

	//var_dump($items_array);

	//var_dump($sub_action, $_SESSION['resume'][$sub_action], $resume_filters[$sub_action]);
	if($items_array) {
		foreach($items_array as $val) {
			switch($sub_action) {
				case 'general':
					break;
				case 'skills':
					$output .= resume_format_dl_item(NULL, $val['resume_skillset_name'], $val['resume_skillset_list'] );
					break;
				case 'employment':
					$output .= resume_format_dl_item($val['resume_employer'], $val['resume_job_title'], $val['resume_start_employ'] . " &ndash; " . (($val['resume_currently_employ'])? "Present" : $val['resume_end_employ']) . "<br />" . $val['resume_job_desc']);
					break;
				case 'education':
					$output .= resume_format_dl_item(NULL, $val['resume_institution'], $val['resume_major']
						.( ($val['resume_minor'] != "Minor" )? " Minor: " . $val['resume_minor']: "")
						. " " . $val['resume_degree']
						. " " . (($val['resume_currently_enrolled'])? "Currently Enrolled" : $val['resume_date_graduated']) );
					break;
				case 'awards':
					$output .= resume_format_dl_item($val['resume_award_title'], $val['resume_award_date'], $val['resume_award_desc']);
					break;
			}
		}
	}

	return $output . "</dl>";
}

function resume_format_dl_item($strong, $title, $desc) {
	return "<dt class=\"part_title\"> " . (($strong)? "<strong>" . $strong . "</strong> ": "") . $title . "</dt>"
		. "<dd class=\"part_desc\">" . $desc . "</dd>";
}

function resume_new() {
	global $resume_filters;

	session_start();

	$sub_action = filter_input(INPUT_POST, 'sub_action', FILTER_SANITIZE_STRING);

	if($sub_action == 'general') {
		$_SESSION['resume'][$sub_action] = filter_input_array(INPUT_POST, $resume_filters[$sub_action]);
	} else {
		$_SESSION['resume'][$sub_action][] = filter_input_array(INPUT_POST, $resume_filters[$sub_action]);
	}

	die(resume_format_dl($_SESSION['resume'][$sub_action], $sub_action));
}

function resume_finalize() {
	global $wpdb;
	global $user_ID;

	global $resume_titles;

	session_start();

	$query = <<< SQL
	insert into %s (
		post_author,
		post_content,
		post_title,
		post_type,
		post_status
	) values (
		"%d",
		"%s",
		"%s",
		'page',
		'draft'
	)
SQL;

	$body = "<h2>" . $_SESSION['resume']['general']['resume_name'] . "</h2>"
		. "<address>" . nl2br($_SESSION['resume']['general']['resume_address']) . "</address>"
		. "<a href=\"mailto:" . $_SESSION['resume']['general']['resume_email'] . "\">" . $_SESSION['resume']['general']['resume_email'] . "</a>"
		. "<a href=\"" . $_SESSION['resume']['general']['resume_website'] . "\">" . $_SESSION['resume']['general']['resume_website'] . "</a>"

		. "<h3>" . $resume_titles['skills'] . "</h3>"
		. resume_format_dl($_SESSION['resume']['skills'], 'skills')

		. "<h3>" . $resume_titles['employment'] . "</h3>"
		. resume_format_dl($_SESSION['resume']['employment'], 'employment')

		. "<h3>" . $resume_titles['education'] . "</h3>"
		. resume_format_dl($_SESSION['resume']['education'], 'education')

		. "<h3>" . $resume_titles['awards'] . "</h3>"
		. resume_format_dl($_SESSION['resume']['awards'], 'awards');

	$wpdb->query(sprintf($query, $wpdb->posts, $user_ID, addslashes($body), $_SESSION['resume']['general']['resume_title']));

	unset($_SESSION['resume']);

	die("");
}

function resume_reset() {
	unset($_SESSION['resume']);
	die();
}

?>