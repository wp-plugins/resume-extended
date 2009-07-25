<? resume_start_form('education') ?>
<script>
	// add the check disabler
	jQuery(document).ready(function () {
		jQuery('#resume_currently_enrolled').check_disables('#resume_date_graduated');
	});
</script>

<div class="control_box">
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

<? resume_end_form('awards', 'Add Degree', 'employment') ?>