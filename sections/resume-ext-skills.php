<?php

require_once('resume-ext-section.php');
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

class resume_ext_skills 
extends resume_ext_section 
implements resume_ext_exportable {

	protected $title = "Skills";
	protected $cta = "Add Skillset";
	protected $id = 'skills';

	protected $count_table = resume_ext_db_manager::name_skillset;

	protected $filters = Array(
		'resume_skillset_name' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_skillset_list' => FILTER_SANITIZE_SPECIAL_CHARS);

	public function format_wp_form($prev, $next) {
		$this->format_start_form(); ?>

		<div class="control_box">
		<div class="form_label">
		Skillset
		<p class="desc">
		Any skills that an employer might be interested in.  You can add
		multiple&nbsp;skillsets.
		</p>
		</div>

		<label for="resume_skillset_name" class="inline_label">The title for the skillset</label>
		<input name="resume_skillset_name" id="resume_skillset_name" type="text" class="form_text" />

		<label for="resume_skillset_list" class="inline_label">Comma separated list of skills.</label>
		<textarea name="resume_skillset_list" id="resume_skillset_list" class="form_textarea"></textarea>

		</div>
		<?

		$this->format_end_form($prev, $next);
	}

	public function create_db() {
		global $wpdb;

		$resume = resume_ext_db_manager::make_name(resume_ext_db_manager::name_resume);
		$skillset = resume_ext_db_manager::make_name(resume_ext_db_manager::name_skillset);
		$skill = resume_ext_db_manager::make_name(resume_ext_db_manager::name_skill);

		maybe_create_table($skillset, sprintf(resume_ext_db_manager::sql_skillset, $skillset, $resume));
		maybe_create_table($skill, sprintf(resume_ext_db_manager::sql_skill, $skill, $skillset));
	}

	public function insert_db() {
		global $wpdb;

		if(isset($_SESSION['resume'][$this->id])) {

			foreach($_SESSION['resume'][$this->id] as $skillset) {
				$wpdb->insert(
					resume_ext_db_manager::make_name(resume_ext_db_manager::name_skillset),
					array(
						'resume_id' => resume_ext_db_manager::$id_resume,
						'name' => $skillset['resume_skillset_name']
					));
				resume_ext_db_manager::$id_skillset = $wpdb->insert_id;

				$skill_list = explode(',',  $skillset['resume_skillset_list']);
				foreach($skill_list as $skill) {
					$wpdb->insert(
						resume_ext_db_manager::make_name(resume_ext_db_manager::name_skill),
						array(
							'skillset_id' => resume_ext_db_manager::$id_skillset,
							'name' => trim($skill),
						));
					resume_ext_db_manager::$id_skill = $wpdb->insert_id;
				}
			}
		}

	}

	public function select_db($resume_id) {
		global $wpdb;

		$skillset = resume_ext_db_manager::make_name(resume_ext_db_manager::name_skillset);
		$skill = resume_ext_db_manager::make_name(resume_ext_db_manager::name_skill);

		$query = sprintf(
				resume_ext_db_manager::sql_select_skills,
				$skillset,
				$skill,
				$resume_id);

		//echo $query;

		return $wpdb->get_results(
			$query,
			ARRAY_A
		);
	}

	public function format_entry_xhtml($val, $key) {
		return $this->format_dl_item(NULL, $val['resume_skillset_name'], $val['resume_skillset_list'] );
	}

} ?>