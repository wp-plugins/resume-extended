<?php

/**
 * Awards Section
 * manage everything that has to do with the awards section.
 *
 * @package resume-extended
 * @subpackage resume-extended-sections
 * @since 0.2
 * @author Aaron Spaulding
 **/

require_once('resume-ext-section.php');

class resume_ext_awards 
extends resume_ext_section 
implements resume_ext_exportable {

	protected $title = "Awards";
	protected $cta = "Add Award";
	protected $id = 'awards';

	protected $count_table = resume_ext_db_manager::name_awards;

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

		<label for="resume_award_date" class="pre_label">Date</label>
		<input name="resume_award_date" id="resume_award_date" type="text" class="form_date one_third" />

		<label for="resume_award_desc" class="inline_label">Description</label>
		<textarea name="resume_award_desc" id="resume_award_desc" class="form_textarea"></textarea>

		</div>
		<?php

		$this->format_end_form($prev, $next);
	}

	public function insert_db() {
		global $wpdb;

		if(isset($_SESSION['resume'][$this->id])) {
			foreach($_SESSION['resume'][$this->id] as $key => $award) {
				// insert the vevent
				$wpdb->insert(
					resume_ext_db_manager::make_name(resume_ext_db_manager::name_vevent),
					array(
						'DTSTART' => strftime("%F", strtotime($award['resume_award_date'])),
						'SUMMARY' => $award['resume_award_title'],
						'DESCRIPTION' => $award['resume_award_desc']
					));
				resume_ext_db_manager::$id_vevent = $wpdb->insert_id;

				// insert the awards entry
				$wpdb->insert(
					resume_ext_db_manager::make_name(resume_ext_db_manager::name_awards),
					array(
						'resume_id' => resume_ext_db_manager::$id_resume,
						'vevent_id' => resume_ext_db_manager::$id_vevent
					));
				resume_ext_db_manager::$id_awards = $wpdb->insert_id;
			}
		}
	}

	public function create_db() {
		$awards = resume_ext_db_manager::make_name(resume_ext_db_manager::name_awards);
		$resume = resume_ext_db_manager::make_name(resume_ext_db_manager::name_resume);
		$vevent = resume_ext_db_manager::make_name(resume_ext_db_manager::name_vevent);

		maybe_create_table($awards, sprintf(resume_ext_db_manager::sql_awards, $awards, $resume, $vevent));
	}

	public function select_db($resume_id) {
		global $wpdb;

		$awards = resume_ext_db_manager::make_name(resume_ext_db_manager::name_awards);
		$vevent = resume_ext_db_manager::make_name(resume_ext_db_manager::name_vevent);

		$query = sprintf(
				resume_ext_db_manager::sql_select_awards,
				$awards,
				$vevent,
				$resume_id);

		$results = $wpdb->get_results(
			$query,
			ARRAY_A
		);
		
		foreach($results as &$r) {
			$r['resume_award_date'] = $this->format_date($r['resume_award_date_timestamp']);
		}

		//echo $query;

		return $results;
	}
	
	/**
	 * The fallback get data function
	 *
	 * @since 0.3
	 * @access public
	 * @returns an associative array of data about the section strong, title, desc
	 */
	public function select_db_fallback($resume_id) {
		$data = $this->select_db($resume_id);
		$val = Array();
		
		foreach($data as $entry) {
			$val[] = Array(
				"strong" => $entry['resume_award_title'],
				"title" => $entry['resume_award_date'],
				"desc" => $entry['resume_award_desc'],
				"subsections" => NULL
			);
		}
		
		return $val;
	}

	public function format_entry_xhtml($val, $key) {
		return $this->format_dl_item($val['resume_award_title'], $val['resume_award_date'], $val['resume_award_desc']);
	}

} ?>
