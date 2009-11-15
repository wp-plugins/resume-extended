<?php

require_once('resume-ext-section.php');
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

class resume_ext_general 
extends resume_ext_section 
implements resume_ext_exportable {
	protected $title = "General Info";
	protected $cta = "Add R&eacute;sum&eacute;";
	protected $id = 'general';

	protected $count_table = resume_ext_db_manager::name_resume;

	protected $filters = Array(
		'resume_title' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_objective' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_name' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_address' => FILTER_SANITIZE_MAGIC_QUOTES,
		'resume_email' => FILTER_VALIDATE_EMAIL,
		'resume_website' => FILTER_VALIDATE_URL);

	public function format_wp_form($prev, $next) {
		$this->format_start_form() ?>
		<div class="control_box">
			<label for="resume_title" class="form_label">R&eacute;sum&eacute; Title<span class="desc"> &middot; A short description</span</label>
			<input name="resume_title" id="resume_title" class="form_text" type="text" />
		</div>

		<div class="control_box">
			<label for="resume_objective" class="form_label">Professional Objective<span class="desc"> &middot; Your goals as a professional.</span></label>
			<textarea name="resume_objective" id="resume_objective" class="form_textarea"></textarea>
		</div>

		<div class="control_box" id="contact_info">
		<div class="form_label">
		Contact Information
		<p class="desc">
		How can potential employers get a hold of you?
		</p>
		</div>

		<label for="resume_name" class="inline_label">Full Name</label>
		<input name="resume_name" id="resume_name" type="text" class="form_text" />

		<label for="resume_address" class="inline_label">Address</label>
		<textarea name="resume_address" id="resume_address" class="form_textarea"></textarea>

		<label for="resume_email" class="inline_label">Email Address</label>
		<input name="resume_email" id="resume_email" type="text" class="form_text"/>

		<label for="resume_website" class="inline_label">Website</label>
		<input name="resume_website" id="resume_website" type="text" class="form_text"/>

		<!-- temporarily leave out phone numbers -->

		</div>

		<?
		$this->format_end_form($prev, $next);
	}

	public function create_db() {
		global $wpdb;

		$resume = resume_ext_db_manager::make_name(resume_ext_db_manager::name_resume);
		$vcard = resume_ext_db_manager::make_name(resume_ext_db_manager::name_vcard);
		$vcard_ci= resume_ext_db_manager::make_name(resume_ext_db_manager::name_vcard_ci);
		$vevent = resume_ext_db_manager::make_name(resume_ext_db_manager::name_vevent);

		maybe_create_table($vcard, sprintf(resume_ext_db_manager::sql_vcard, $vcard));
		maybe_create_table($vcard_ci, sprintf(resume_ext_db_manager::sql_vcard_ci, $vcard_ci, $vcard));
		maybe_create_table($vevent, sprintf(resume_ext_db_manager::sql_vevent, $vevent));
		maybe_create_table($resume, sprintf(resume_ext_db_manager::sql_resume, $resume, $vcard, $wpdb->posts));

		add_option(RESUME_EXTENDED_NAME . "database_version", RESUME_EXTENDED_VERSION);
	}

	public function insert_db() {
		global $wpdb;
		$wpdb->insert(
			resume_ext_db_manager::make_name(resume_ext_db_manager::name_vcard),
			array(
				'FN' => $_SESSION['resume'][$this->id]['resume_name'],
				'URL' => $_SESSION['resume'][$this->id]['resume_website']
			));
		resume_ext_db_manager::$id_vcard = $wpdb->insert_id;

		$wpdb->insert(
			resume_ext_db_manager::make_name(resume_ext_db_manager::name_vcard_ci),
			array(
				'TEL' => NULL,
				'EMAIL' => $_SESSION['resume'][$this->id]['resume_email'],
				'LABEL' => $_SESSION['resume'][$this->id]['resume_address'],
				'pref' => true,
				'info_type' => 'home',
				'vcard_id' => resume_ext_db_manager::$id_vcard
			));
		resume_ext_db_manager::$id_vcard_ci = $wpdb->insert_id;

		$wpdb->insert(
			resume_ext_db_manager::make_name(resume_ext_db_manager::name_resume),
			array(
				'title' => $_SESSION['resume'][$this->id]['resume_title'],
				'vcard_id' => resume_ext_db_manager::$id_vcard,
				'objective' => $_SESSION['resume'][$this->id]['resume_objective']
			));
		resume_ext_db_manager::$id_resume = $wpdb->insert_id;
	}

	public function select_db($resume_id) {
		global $wpdb;

		$resume = resume_ext_db_manager::make_name(resume_ext_db_manager::name_resume);
		$vcard = resume_ext_db_manager::make_name(resume_ext_db_manager::name_vcard);
		$vcard_ci= resume_ext_db_manager::make_name(resume_ext_db_manager::name_vcard_ci);

		$query = sprintf(
				resume_ext_db_manager::sql_select_general,
				$resume,
				$vcard,
				$vcard_ci,
				$resume_id);

		//echo $query;

		return $wpdb->get_row(
			$query,
			ARRAY_A
		);
	}

	/**
	 * Update the last resume with its page id
	 *
	 * This function will not work correctly across page reloads.
	 *
	 * @since 0.2
	 * @param $page_id int the id of the page that was just created
	 *
	 */
	static public function update_last_resume_with_page_id($page_id) {
		global $wpdb;
		$wpdb->update(
			resume_ext_db_manager::make_name(resume_ext_db_manager::name_resume),
			array(
				'page_id' => $page_id
			),
			array(
				'resume_id' => resume_ext_db_manager::$id_resume
			),
			array(
				'%d'
			)
		);
	}

	public function format_entry_xhtml($val, $key) {}

	public function format_wp_xhtml($resume_id, $data) {

		if(!$data) {
			$data = $this->select_db($resume_id);
		}
		return "<h2>" . $data['resume_name'] . "</h2>"
		. "<address>" . nl2br($data['resume_address']) . "</address>"
		. "<a href=\"mailto:" . $data['resume_email'] . "\">" . $data['resume_email'] . "</a><br />"
		. "<a href=\"" . $data['resume_website'] . "\">" . $data['resume_website'] . "</a>"
		. '<div id="resume_ext_objective">' . $data['resume_objective'] . "</div>";
	}

	public function add_data() {
		session_start();

		$_SESSION['resume'][$this->id] = $this->filter_data();
	}

}?>