<?php

require_once('resume-ext-section.php');

class resume_ext_general extends resume_ext_section {
	protected $title = "General Info";
	protected $cta = "Add Resume";
	protected $id = 'general';

	protected $filters = Array(
		'resume_title' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_objective' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_name' => FILTER_SANITIZE_SPECIAL_CHARS,
		'resume_address' => FILTER_SANITIZE_MAGIC_QUOTES,
		'resume_email' => FILTER_VALIDATE_EMAIL,
		'resume_website' => FILTER_VALIDATE_URL);

	public function format_wp_form($prev, $next) {
		$this->format_start_form() ?>
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

		<?
		$this->format_end_form($prev, $next);
	}

	public function create_db() {

	}

	public function format_entry_xhtml($val, $key) {}

	public function format_wp_xhtml() {
		return "<h2>" . $_SESSION['resume'][$this->id]['resume_name'] . "</h2>"
		. "<address>" . nl2br($_SESSION['resume'][$this->id]['resume_address']) . "</address>"
		. "<a href=\"mailto:" . $_SESSION['resume'][$this->id]['resume_email'] . "\">" . $_SESSION['resume'][$this->id]['resume_email'] . "</a><br />"
		. "<a href=\"" . $_SESSION['resume'][$this->id]['resume_website'] . "\">" . $_SESSION['resume'][$this->id]['resume_website'] . "</a>"
		. '<div id="resume_ext_objective">' . $_SESSION['resume'][$this->id]['resume_objective'] . "</div>";
	}

	public function add_data() {
		session_start();

		$_SESSION['resume'][$this->id] = $this->filter_data();
	}

}?>