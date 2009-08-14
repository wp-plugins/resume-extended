<?php

require_once('resume-ext-section.php');

class resume_ext_finish extends resume_ext_section {
	protected $title = "Finish";
	protected $cta = "";
	protected $id = 'finish';

	public function format_wp_form($prev, $next) {
		global $resume_ajax;
?>
	<p>
	If you&rsquo;re finished, go ahead and create your r&eacute;sum&eacute;.  It will save it to the worpress database and make a draft.  To publish it to your blog, go to Pages &rsaquo; Edit and select you r&eacute;sum&eacute; and click publish.
	</p>

	<form action="<?= $resume_ajax ?>" method="POST" id="resume_submit">
		<div>
		<input type="hidden" name="action" value="resume_finalize" />
		<input type="submit" class="button-primary" value="Create R&eacute;sum&eacute &raquo;" /><div id="resume_ext_finished"></div>
		</div>
	</form>

	<p>
	If you want to start again, you can do that too.
	</p>

	<form action="<?= $resume_ajax ?>" method="POST" id="resume_reset">
		<div class="submit">
		<input type="hidden" name="action" value="resume_reset" />
		<input type="submit" value="Reset R&eacute;sum&eacute" />
		</div>
	</form>

<?php
	}

	public function create_db() {}
	public function insert_db() {}

	public function format_entry_xhtml($val, $key) {
		return "";
	}

	public function add_data() {}

	public function format_wp_xhtml() {
		return "";
	}
}
?>