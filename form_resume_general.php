<? resume_start_form('general') ?>
<div class="control_box">
	<label for="resume_title" class="form_label">Resume Title<span class="desc"> &middot; A short description</span</label>
	<input name="resume_title" id="resume_title" class="form_text" type="text" />
</div>

<div class="control_box">
	<label for="resume_objective" class="form_label">Professional Objective<span class="desc"> &middot; Your goals as a professional.</span></label>
	<textarea name="resume_objective" id="resume_objective" class="form_textarea"></textarea>
</div>

<div class="control_box" id="contact_info">
<div class="form_label">
Contact Information
<p class="desc">
How can potential employers get a hold of you?
</p>
</div>

<label for="resume_name" class="inline_label">Full Name</label>
<input name="resume_name" id="resume_name" type="text" class="form_text" />

<label for="resume_address" class="inline_label">Address</label>
<textarea name="resume_address" id="resume_address" class="form_textarea"></textarea>

<label for="resume_email" class="inline_label">Email Address</label>
<input name="resume_email" id="resume_email" type="text" class="form_text"/>

<label for="resume_website" class="inline_label">Website</label>
<input name="resume_website" id="resume_website" type="text" class="form_text"/>

<!-- temporarily leave out phone numbers -->

</div>

<? resume_end_form('skills', 'Add Resume') ?>