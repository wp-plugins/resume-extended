<?php
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

	public function format_wp_form($prev, $next) {
		$this->format_start_form() ?>
		<script>
			// add the check disabler
			jQuery(document).ready(function () {
				jQuery('#resume_currently_employ').check_disables('#resume_end_employ');
			});
		</script>

		<div class="control_box">
			<label for="resume_employer" class="form_label">Employer<span class="desc"> &middot; The name of the company you worked for.</span</label>
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

	public function create_db() {}

	public function format_entry_xhtml($val) {
		return $this->format_dl_item($val['resume_employer'], $val['resume_job_title'], $val['resume_start_employ'] . " &ndash; " . (($val['resume_currently_employ'])? "Present" : $val['resume_end_employ']) . "<br />" . $val['resume_job_desc']);
	}
}
?>