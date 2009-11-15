<?php

require_once('resume-ext-section.php');

class resume_ext_education 
extends resume_ext_section 
implements resume_ext_exportable {
	protected $title = "Education";
	protected $cta = "Add Degree";
	protected $id = 'education';

	protected $count_table = resume_ext_db_manager::name_degree;

	protected $filters = Array(
		'resume_institution' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_major' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_minor' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_degree' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_currently_enrolled' => FILTER_VALIDATE_BOOLEAN,
		'resume_date_graduated' => FILTER_SANITIZE_SPECIAL_CHARS);

	public function format_wp_form($prev, $next) {
		$this->format_start_form() ?>
		<script>
			// add the check disabler
			jQuery(document).ready(function () {
				jQuery('#resume_currently_enrolled').check_disables('#resume_date_graduated');
			});
		</script>

		<div class="control_box">
			<div class="form_label">
			Degree
			<p class="desc">
			A degree from an accredited college or university.
			</p>
			</div>

			<label for="resume_institution" class="inline_label">Institution</label>
			<input name="resume_institution" id="resume_institution" type="text" class="form_text" /><br />

			<label for="resume_major" class="inline_label">Major</label>
			<input name="resume_major" id="resume_major" type="text" class="form_text two_thirds" />

			<label for="resume_degree" class="inline_label">Degree</label>
			<input name="resume_degree" id="resume_degree" type="text" class="form_text one_third" /><br />

			<label for="resume_minor" class="inline_label">Minor</label>
			<input name="resume_minor" id="resume_minor" type="text" class="form_text two_thirds" />

			<label for="resume_date_graduated" class="form_label">Date Graduated<span class="desc"> &middot; When did you get this degree.</span></label>
			<input name="resume_date_graduated" id="resume_date_graduated" class="form_date" type="text" />
			<input name="resume_currently_enrolled" id="resume_currently_enrolled" type="checkbox" />
			<label for="resume_currently_enrolled">Currently Enrolled</label>
		</div>

		<?
		$this->format_end_form($prev, $next);
	}

	public function create_db() {
		$degree = resume_ext_db_manager::make_name(resume_ext_db_manager::name_degree);
		$resume = resume_ext_db_manager::make_name(resume_ext_db_manager::name_resume);
		$vevent = resume_ext_db_manager::make_name(resume_ext_db_manager::name_vevent);

		maybe_create_table($degree, sprintf(resume_ext_db_manager::sql_degree, $degree, $resume, $vevent));
	}

	public function insert_db() {
		global $wpdb;

		if(isset($_SESSION['resume'][$this->id])) {

			foreach($_SESSION['resume'][$this->id] as $edu) {
				// insert the vevent
				$wpdb->insert(
					resume_ext_db_manager::make_name(resume_ext_db_manager::name_vevent),
					array(
						'DTEND' => strftime("%F", strtotime($edu['resume_date_graduated'])),
					));
				resume_ext_db_manager::$id_vevent = $wpdb->insert_id;

				$wpdb->insert(
					resume_ext_db_manager::make_name(resume_ext_db_manager::name_degree),
					array(
						'resume_id' => resume_ext_db_manager::$id_resume,
						'vevent_id' => resume_ext_db_manager::$id_vevent,
						'institution' => $edu['resume_institution'],
						'major' => $edu['resume_major'],
						'minor' => $edu['resume_minor'],
						'level' => $edu['resume_degree'],
						'enrolled' => $edu['resume_currently_enrolled']
					));
				resume_ext_db_manager::$id_degree = $wpdb->insert_id;
			}

		}
	}

	public function select_db($resume_id) {
		global $wpdb;

		$degree = resume_ext_db_manager::make_name(resume_ext_db_manager::name_degree);
		$vevent = resume_ext_db_manager::make_name(resume_ext_db_manager::name_vevent);

		$query = sprintf(
				resume_ext_db_manager::sql_select_education,
				$degree,
				$vevent,
				$resume_id);

		//echo $query;

		return $wpdb->get_results(
			$query,
			ARRAY_A
		);
	}

	public function format_entry_xhtml($val, $key) {
		return $this->format_dl_item(NULL, $val['resume_institution'], $val['resume_major']
			.( (($val['resume_minor'] != "Minor" ) && ($val['resume_minor'] != "" ))? " Minor: " . $val['resume_minor']: "")
			. " " . $val['resume_degree']
			. " &ndash; " . (($val['resume_currently_enrolled'])? "Currently Enrolled" : $val['resume_date_graduated']) );

	}

}
?>