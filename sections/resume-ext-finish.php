<?php

require_once('resume-ext-section.php');

class resume_ext_finish extends resume_ext_section {
	protected $title = "Finish";
	protected $cta = "";
	protected $id = 'finish';

	public function format_wp_form($prev, $next) {
		global $resume_ajax;
?>
	<form action="<?= $resume_ajax ?>" method="POST" id="resume_reset">
		<div class="submit">
		<input type="hidden" name="action" value="resume_reset" />
		<input type="submit" value="Reset Resume" />
		</div>
	</form>
	<form action="<?= $resume_ajax ?>" method="POST" id="resume_submit">
		<div>
		<input type="hidden" name="action" value="resume_finalize" />
		<input type="submit" class="button-primary" value="Create Resume &raquo;" />
		</div>
	</form>
<?php
	}

	public function create_db() {}

	public function format_entry_xhtml($val) {
		return "";
	}

	public function add_data() {}

	public function format_wp_xhtml() {
		return "";
	}
}
?>