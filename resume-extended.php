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


Plugin Name: Resume Extended
Plugin URI: http://sachimp/
Description: Create and manage your Resume all from your blog.
Version: 0.2.2
Author: Aaron Spaulding
Author URI: http://sachimp.com/
*/

/**
 * Version 0.2.2
 */
define("RESUME_EXTENDED_NAME", "resume_ext_");
define("RESUME_EXTENDED_VERSION", 2);
define("RESUME_EXTENDED_VERSION_MINOR", 2);
define("RESUME_EXTENDED_VERSION_PRETTY", "0.2.2");

/**
 * Determine the location
 */

define("RESUME_EXT_PATH", WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/');
define("RESUME_EXT_ADMIN_PATH", get_bloginfo('wpurl') . "/wp-admin/");
define("RESUME_EXT_AJAX_PATH", RESUME_EXT_ADMIN_PATH . "admin-ajax.php");

define("RESUME_EXT_NEST_OFFSET", 3);

global $resume_path;
global $resume_ajax;
global $resume_sections;

$resume_path = RESUME_EXT_PATH;
$resume_ajax = RESUME_EXT_AJAX_PATH;

require_once('resume-ext-db-manager.php');

include_once('sections/resume-ext-general.php');
include_once('sections/resume-ext-skills.php');
include_once('sections/resume-ext-employment.php');
include_once('sections/resume-ext-education.php');
include_once('sections/resume-ext-awards.php');
include_once('sections/resume-ext-finish.php');

$resume_sections = Array(
	new resume_ext_general(0),
	new resume_ext_skills(1),
	new resume_ext_employment(2),
	new resume_ext_education(3),
	new resume_ext_awards(4),
	new resume_ext_finish(5)
);

add_filter('admin_menu', 'resume_menu');
add_filter('the_content', 'resume_ext_content');

add_action('admin_print_styles', 'resume_admin_styles');
add_action('admin_print_scripts', 'resume_admin_scripts');


function resume_admin_styles () {
	// outright stolen from NextGEN
	if(!isset($_GET['page']))
		return;

	global $resume_path;

	switch($_GET['page']) {
		case 'resume_new_page':
			wp_enqueue_style('resume_admin_css', $resume_path . "admin_styles.css");
			wp_enqueue_style('resume_ui_smoothness', $resume_path . "css/smoothness/jquery-ui-1.7.2.custom.css");
			break;
	}
}

function resume_admin_scripts () {
	// outright stolen from NextGEN
	if(!isset($_GET['page']))
		return;

	global $resume_path;

	switch($_GET['page']) {
		case 'resume_new_page':
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
			break;
	}
}

function resume_ext_content($content) {
	$regex = "/\s*\[resume-ext id=\"(\d+)\"]\s*/";
	//$regex = "/resume-ext/";
	$content = preg_replace_callback($regex, 'resume_ext_make_body', $content);

	return $content;
}

function resume_ext_make_body($matches) {
	global $resume_sections;

	// wordpress wraps the first tag in a <p> use a decoy to before injecting the stylesheet
	$body = '
	<style>
	.hresume ul li:before{
		content: "";
	}

	.hresume ul {
		text-indent: 0 !important;
	}

	.hresume h4 {
		margin: 0;
		padding-left: 10px;
	}

	.hresume .part_desc {
		color: #727272;
	}

	.hresume > ul li  {
		margin-left: 0 !important;
		padding-left: 0 !important;
	}
	</style>
	<div class="hresume">';

	foreach($resume_sections as $sect) {
		$body .= $sect->format_wp_xhtml($matches[1], NULL);
	}

	return $body . "\n</div>";
}

function resume_menu()  {

	add_menu_page('Add New', 'R&eacute;sum&eacute; Ext.', 8, 'resume_new_page', 'resume_new_page');
	add_submenu_page('resume_new_page', 'Add New', 'Add New', 8, 'resume_new_page', 'resume_new_page');

	//add_options_page('Resume Options', 'Resume', 8, 'resumeoptions', 'resume_options');
}

/*function resume_options() { ?>
<div class="wrap">
	<h2>Resume Options</h2>
</div>
<? }*/

function resume_new_page() {
	global $resume_sections;

	$do_next = filter_input(INPUT_POST, 'do_next', FILTER_SANITIZE_STRING);


?>
	<div class="wrap resume_wrap">
	<h2>New R&eacute;sum&eacute;</h2>
	<div id="tabs">

	<ul class="tab_labels">
<?
	foreach($resume_sections as $sect) {
?>
		<li><a href="#tab-<?= $sect->get_id() ?>"><?= $sect->get_title() ?></a></li>
<?
	}
?>	</ul><?

	foreach($resume_sections as $i => $sect) {
		//var_dump($sect);
?>		<div id="tab-<?= $sect->get_id() ?>"> <?
		$sect->format_wp_form($resume_sections[$i - 1], $resume_sections[$i + 1]);
?>		</div><?
	}
?>

	</div>
	</div>
<?
}
/*
function resume_error($errno, $errstr, $errfile, $errline, $errcontext) {
	$errortype = array (
			E_ERROR              => 'Error',
			E_WARNING            => 'Warning',
			E_PARSE              => 'Parsing Error',
			E_NOTICE             => 'Notice',
			E_CORE_ERROR         => 'Core Error',
			E_CORE_WARNING       => 'Core Warning',
			E_COMPILE_ERROR      => 'Compile Error',
			E_COMPILE_WARNING    => 'Compile Warning',
			E_USER_ERROR         => 'User Error',
			E_USER_WARNING       => 'User Warning',
			E_USER_NOTICE        => 'User Notice',
			E_STRICT             => 'Runtime Notice',
			E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
			);
	$errstr .= "\n  File: " . $errfile . "\n  Line: " . $errline;
	if(class_exists(FB) && (ob_get_level() > 0)) {
		FB::error($errstr, $errortype[$errno]);
		FB::info($errcontext, $errortype[$errno] . " Context");
		FB::trace($errortype[$errno] . " Stack Trace");
	}
}

function resume_db_error($str, $query) {
	if(class_exists(FB) && (ob_get_level() > 0)) {
		$errstr = $str . "  Query: " . $query;
		FB::error($errstr, "WP DB Error");
		FB::trace("WP DB Error");
	}
}

// this may surface some errors that aren't fixable
//   see bug: #10536
set_error_handler(resume_error);
$wpdb->hook_error('resume_db_error');
$wpdb->show_errors();*/

function resume_ext_install() {
	global $wpdb;
	global $resume_sections;

	foreach( $resume_sections as $sect ) {

		$sect->create_db();
	}
}

register_activation_hook(__FILE__, 'resume_ext_install');

add_action('wp_ajax_resume_new', 'resume_new');
add_action('wp_ajax_resume_new_project', 'resume_new_project');
add_action('wp_ajax_resume_finalize', 'resume_finalize');
add_action('wp_ajax_resume_reset', 'resume_reset');

function resume_new() {
	global $resume_sections;

	session_start();

	$sub_action = filter_input(INPUT_POST, 'sub_action', FILTER_SANITIZE_NUMBER_INT);

	//var_dump($resume_sections, $sub_action, $_SESSION);

	$resume_sections[$sub_action]->add_data();

	die($resume_sections[$sub_action]->format_wp_admin_xhtml());
}

function resume_new_project() {
	global $resume_sections;

	session_start();

	$sub_action = filter_input(INPUT_POST, 'sub_action', FILTER_SANITIZE_NUMBER_INT);

	//var_dump($resume_sections, $sub_action, $_SESSION);

	$prj = new resume_ext_projects($sub_action);
	$prj->add_data();

	die($prj->format_wp_admin_xhtml());
}

function resume_finalize() {
	global $wpdb;
	global $user_ID;

	global $resume_sections;

	session_start();

	$query = "
	insert into %s (
		post_author,
		post_content,
		post_title,
		post_type,
		post_status
	) values (
		'%d',
		'%s',
		'%s',
		'page',
		'draft'
	)";

	$body = "";
	$page_title = "";

	foreach($resume_sections as $sect) {
		$sect->insert_db();
		//$body .= $sect->format_wp_xhtml();
		if(is_a($sect, 'resume_ext_general')) {
			$page_title = $_SESSION['resume'][$sect->get_id()]['resume_title'];
			$body = '[resume-ext id="' . resume_ext_db_manager::$id_resume . '"]';
		}
	}

	//var_dump($body);

	$wpdb->query(sprintf($query, $wpdb->posts, $user_ID, addslashes($body), $page_title));

	resume_ext_general::update_last_resume_with_page_id($wpdb->insert_id);

	unset($_SESSION['resume']);

	die('{ page_id: "' . RESUME_EXT_ADMIN_PATH . "page.php?action=edit&post=".$wpdb->insert_id . '" }');
}

function resume_reset() {
	session_start();
	unset($_SESSION['resume']);
	die();
}

?>
