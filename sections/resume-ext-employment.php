<?php

require_once('resume-ext-section.php');
require_once('resume-ext-projects.php');

class resume_ext_employment extends resume_ext_section {
	protected $title = "Employment History";
	protected $cta = "Add Employer";
	protected $id = 'employment';

	protected $filters = Array(
		'resume_employer' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_job_title' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_start_employ' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_end_employ' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_currently_employ' => FILTER_VALIDATE_BOOLEAN,
		'resume_job_desc' => FILTER_SANITIZE_SPECIAL_CHARS);

	protected $projects = Array();

	public function format_wp_form($prev, $next) {
		$this->format_start_form() ?>
		<script>
			// add the check disabler
			jQuery(document).ready(function () {
				jQuery('#resume_currently_employ').check_disables('#resume_end_employ');
			});
		</script>

		<div class="control_box">
			<label for="resume_employer" class="form_label">Employer<span class="desc"> &middot; The name of the company you worked for.</span></label>
			<input name="resume_employer" id="resume_employer" class="form_text" type="text" />
		</div>

		<div class="control_box">
			<label for="resume_job_title" class="form_label">Job Title<span class="desc"> &middot; Your last job title.</span></label>
			<input name="resume_job_title" id="resume_job_title" class="form_text" type="text" />
		</div>

		<div class="control_box">
			<label for="resume_start_employ" class="form_label">Date Employed<span class="desc"> &middot; When you were employed with the company.</span></label>
			<input name="resume_start_employ" id="resume_start_employ" class="form_date" type="text" /> &ndash;
			<input name="resume_end_employ" id="resume_end_employ" class="form_date" type="text" />
			<input name="resume_currently_employ" id="resume_currently_employ" type="checkbox" />
			<label for="resume_currently_employ">Present</label>
		</div>

		<div class="control_box">
		<div class="form_label">
		Job Description
		<p class="desc">
		A short description of your responsibilities.
		</p>
		<textarea name="resume_job_desc" id="resume_job_desc" class="form_textarea"></textarea>
		</div>


		</div>
		<? $this->format_end_form($prev, $next);
	}

	public function create_db() {
		$resume = resume_ext_db_manager::make_name(resume_ext_db_manager::name_resume);
		$vevent = resume_ext_db_manager::make_name(resume_ext_db_manager::name_vevent);
		$eh = resume_ext_db_manager::make_name(resume_ext_db_manager::name_eh);

		$proj = new resume_ext_projects(0);

		maybe_create_table($eh, sprintf(resume_ext_db_manager::sql_employment_history, $eh, $resume, $vevent));

		$proj->create_db();
	}

	public function insert_db() {
		global $wpdb;

		if(isset($_SESSION['resume'][$this->id])) {
			foreach($_SESSION['resume'][$this->id] as $key => $employment) {

				// insert the vevent
				$wpdb->insert(
					resume_ext_db_manager::make_name(resume_ext_db_manager::name_vevent),
					array(
						'DTSTART' => strftime("%F", strtotime($employment['resume_start_employ'])),
						'DTEND' => strftime("%F", strtotime($employment['resume_end_employ'])),
						'DESCRIPTION' => $employment['resume_job_desc']
					));
				resume_ext_db_manager::$id_vevent = $wpdb->insert_id;

				// insert the emplyment history record
				$wpdb->insert(
					resume_ext_db_manager::make_name(resume_ext_db_manager::name_eh),
					array(
						'resume_id' => resume_ext_db_manager::$id_resume,
						'vevent_id' => resume_ext_db_manager::$id_vevent,
						'employer' => $employment['resume_employer'],
						'title' => $employment['resume_job_title'],
						'status' => $employment['resume_currently_employ']
					));
				resume_ext_db_manager::$id_eh = $wpdb->insert_id;

				$prj = new resume_ext_projects($key);
				$prj->insert_db();
			}
		}

	}

	public function select_db($resume_id) {
		global $wpdb;

		$vevent = resume_ext_db_manager::make_name(resume_ext_db_manager::name_vevent);
		$eh = resume_ext_db_manager::make_name(resume_ext_db_manager::name_eh);

		$query = sprintf(
				resume_ext_db_manager::sql_select_employment,
				$eh,
				$vevent,
				$resume_id );

		//echo $query;

		return $wpdb->get_results(
			$query,
			ARRAY_A
		);
	}

	public function format_wp_admin_xhtml() {
		$output = "<h" . ($this->nest_level + RESUME_EXT_NEST_OFFSET) . ">" . $this->title . "</h" . ($this->nest_level + RESUME_EXT_NEST_OFFSET) . ">" . "<ul>";

		session_start();

		if($_SESSION['resume'][$this->id]) {
			foreach($_SESSION['resume'][$this->id] as $key => $val) {
				$project = new resume_ext_projects($key);

				$output .= $this->format_entry_xhtml($val, $key);

				if(isset($_SESSION['resume'][$project->get_id()])) {
					$output .= $project->format_wp_admin_xhtml();
				}

				ob_start();
				$project->format_wp_form(NULL, NULL);
				$output .= ob_get_contents();
				ob_end_clean();

			}
		}

		return $output . "</ul>";
	}

	public function format_wp_xhtml($resume_id, $data) {
		$output = "<h" . ($this->nest_level + RESUME_EXT_NEST_OFFSET) . ">" . $this->title . "</h" . ($this->nest_level + RESUME_EXT_NEST_OFFSET) . ">" . "<ul>";

		if(!$data && !is_array($data)) {
			$data = $this->select_db($resume_id);
		}

		foreach($data as $key => $val) {
			$project = new resume_ext_projects($key);

			$output .= $this->format_entry_xhtml($val, $key);
			//if(isset($_SESSION['resume'][$project->get_id()])) {
				$output .= $project->format_wp_xhtml($val['employment_history_id'], NULL);
			//}

		}

		return $output . "</ul>";
	}

	public function format_entry_xhtml($val, $key) {
		return $this->format_dl_item($val['resume_employer'], $val['resume_job_title'], $val['resume_start_employ'] . " &ndash; " . (($val['resume_currently_employ'])? "Present" : $val['resume_end_employ']) . "<br />" . $val['resume_job_desc']);
	}
}
?>