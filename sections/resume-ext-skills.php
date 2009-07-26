<?php

require_once('resume-ext-section.php');

class resume_ext_skills extends resume_ext_section {

	protected $title = "Skills";
	protected $cta = "Add Skillset";
	protected $id = 'skills';

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

	public function create_db() {}

	public function format_entry_xhtml($val) {
		return $this->format_dl_item(NULL, $val['resume_skillset_name'], $val['resume_skillset_list'] );
	}

} ?>