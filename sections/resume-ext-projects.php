<?php

require_once('resume-ext-section.php');

class resume_ext_projects extends resume_ext_section {

	protected $title = "Projects";
	protected $cta = "Add Project";
	protected $id = 'projects';

	protected $wp_action = "resume_new_project";
	protected $employer_idx = "resume_new_project";

	protected $filters = Array(
		'resume_project_name' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_project_desc' => FILTER_SANITIZE_SPECIAL_CHARS);

	public function format_wp_form($prev, $next) {
		$this->format_start_form(); ?>

		<div class="control_box">
		<div class="form_label">
		<strong><?= $this->title ?></strong>
		</div>

		<input type="hidden" name="resume_project_employer" value="<?= $this->employer_idx ?>" />

		<label for="resume_project_name" class="inline_label">The title for the skillset</label>
		<input name="resume_project_name" id="resume_project_name" type="text" class="form_text" />

		<label for="resume_project_desc" class="inline_label">Comma separated list of skills.</label>
		<textarea name="resume_project_desc" id="resume_project_desc" class="form_textarea"></textarea>

		</div>
		<?

		$this->format_end_form(NULL, NULL);
	}

	public function create_db() {
		$projects = resume_ext_db_manager::make_name(resume_ext_db_manager::name_projects);
		$eh = resume_ext_db_manager::make_name(resume_ext_db_manager::name_eh);

		maybe_create_table($projects, sprintf(resume_ext_db_manager::sql_projects, $projects, $eh));
	}

	public function insert_db() {
		global $wpdb;

		if(isset($_SESSION['resume'][$this->id])) {
			foreach($_SESSION['resume'][$this->id] as $key => $employment) {

				// insert the emplyment history record
				$wpdb->insert(
					resume_ext_db_manager::make_name(resume_ext_db_manager::name_eh),
					array(
						'employment_history_id' => resume_ext_db_manager::$id_eh,
						'name' => $employment['resume_project_name'],
						'description' => $employment['resume_project_desc']
					));
				resume_ext_db_manager::$id_eh = $wpdb->insert_id;

			}
		}
	}

	public function format_entry_xhtml($val, $key) {
		return $this->format_dl_item(NULL, $val['resume_project_name'], $val['resume_project_desc'] );
	}

} ?>