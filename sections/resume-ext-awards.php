<?php

require_once('resume-ext-section.php');

class resume_ext_awards extends resume_ext_section {

	protected $title = "Awards";
	protected $cta = "Add Award";
	protected $id = 'awards';

	protected $filters = Array(
		'resume_award_title' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_award_date' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_award_desc' => FILTER_SANITIZE_SPECIAL_CHARS);

	public function format_wp_form($prev, $next) {

		$this->format_start_form('awards') ?>
		<div class="control_box">
		<div class="form_label">
		Awards &amp; Honors
		<p class="desc">
		Any awards or honors that would set you apart from the other candidates.
		</p>
		</div>

		<label for="resume_award_title" class="inline_label">Title</label>
		<input name="resume_award_title" id="resume_award_title" type="text" class="form_text" />

		<label for="resume_award_date" class="inline_label">Date</label>
		<input name="resume_award_date" id="resume_award_date" type="text" class="form_date one_third" />

		<label for="resume_award_desc" class="inline_label">Description</label>
		<textarea name="resume_award_desc" id="resume_award_desc" class="form_textarea"></textarea>

		</div>
		<?

		$this->format_end_form($prev, $next);
	}

	public function create_db() {}

	public function format_entry_xhtml($val) {
		return $this->format_dl_item($val['resume_award_title'], $val['resume_award_date'], $val['resume_award_desc']);
	}

} ?>