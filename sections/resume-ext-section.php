<?php
/**
 * Base Class for all Sections
 * implement the basics for a section that does nothing
 *
 * @package resume-extended
 * @subpackage resume-extended-sections
 * @since 0.2
 * @author Aaron Spaulding
 **/

abstract class resume_ext_section {
	/**
	 * The copy for the title of the section
	 * 
	 * @var string
	 * @since 0.2
	 * @author Aaron Spaulding
	 */
	protected $title = "Generic Un-named section";
	
	/**
	 * The copy for the call to action to save the section
	 * 
	 * @var string
	 * @since 0.2
	 * @author Aaron Spaulding
	 */
	protected $cta = "Add Generic";
	
	/**
	 * The id of the section
	 *
	 * @var string
	 * @since 0.2
	 * @author Aaron Spaulding
	 */
	protected $id = 'generic';

	/**
	 * The level of nested sections, used for properly creating headings
	 *
	 * @var integer
	 * @since 0.2
	 * @author Aaron Spaulding
	 */
	protected $nest_level = 0;

	/**
	 * the action to take when the form is submitted
	 *
	 * @var string
	 * @since 0.2
	 * @author Aaron Spaulding
	 */
	protected $wp_action = "resume_new";

	/**
	 * the position of the object in the array
	 *
	 * @deprecated
	 * @var integer
	 * @since 0.2
	 * @author Aaron Spaulding
	 */
	protected $index = 0;

	/**
	 * the table used to count the number of entries in this section
	 *
	 * @var string
	 * @since 0.2
	 * @author Aaron Spaulding
	 */
	protected $count_table = "";
	
	/**
	 * the id used to filter the list of ventries
	 *
	 * @var string
	 * @since 0.2
	 * @author Aaron Spaulding
	 */
	protected $count_table_id = "resume_id";

	/**
	 * The filters to be used in the filter_input_array() function
	 *
	 * @since 0.2
	 * @author Aaron Spaulding
	 */
	protected $filters = FILTER_SANITIZE_SPECIAL_CHARS;
	
	/**
	 * filters the POST data according to the {@link $filters} variable
	 *
	 * @return array the filtered results of a form post
	 * @author Aaron Spaulding
	 */
	protected function filter_data() {
		return filter_input_array(INPUT_POST, $this->filters);
	}
	
	/**
	 * Format a date according to the users preferred date format_id
	 *
	 * @param timestamp the unix timestamp
	 * @since 0.3
	 * @author Aaron Spaulding
	 */
	
	protected function format_date($timestamp) {
		return date(get_option('date_format'), $timestamp);
	
	}

	/**
	 * formats an entry as a list item
	 *
	 * @since 0.2
	 * @param string $strong the emphasized part of the title
	 * @param string $title  the rest of the title
	 * @param string $desc the description of the entry
	 * @return string the formatted item
	 * @author Aaron Spaulding
	 */
	protected function format_dl_item($strong, $title, $desc)  {
		return '<li><div class="part_title" style="width:100%">' . (($strong)? "<strong>" . $strong . "</strong> ": "") . $title . "</div>"
			. '<div class="part_desc" style="width:100%">' . $desc . "</div></li>";
	}

	/**
	 * the starting part of the form
	 *
	 * @since 0.2
	 * @return void
	 * @author Aaron Spaulding
	 */
	protected function format_start_form() {
		global $resume_ajax; ?>
		<div class="sub_form">
		<div id="resume_<?php echo $this->id ?>_target" class="preview_target">
		</div>
		<form action="<?php echo $resume_ajax ?>" method="post" id="resume_<?php echo $this->id ?>">
			<input type="hidden" name="action" value="<?php echo $this->wp_action ?>" />
			<input type="hidden" name="sub_action" value="<?php echo $this->index ?>" />
<?php
	}

	protected function format_end_form($prev, $next) {?>
		<script type="text/javascript">(function($) {
			$(document).ready(function () {
		<?php if($next) { ?>
				$("#next_page_<?php echo $next->get_id() ?>").click(function() {
					$("#tabs").tabs('select', '#tab-<?php echo $next->get_id() ?>');
				});
		<?php }

		if($prev) { ?>
				$("#prev_page_<?php echo $prev->get_id() ?>").click(function() {
					$("#tabs").tabs('select', '#tab-<?php echo $prev->get_id() ?>');
				});
		<?php } ?>
			});

			})(jQuery);</script>
			<p class="submit">
		<?php if($prev) { ?>
				<input type="button" id="prev_page_<?php echo $prev->get_id() ?>" value="&laquo;"/>
		<?php } ?>
				<input type="submit" id="ajax_action" value="<?php echo $this->cta ?>" />

		<?php if($next) { ?>
				<input type="button" id="next_page_<?php echo $next->get_id() ?>" class="button-primary" value="<?php echo $next->get_title() ?> &raquo;"/>
		<?php } ?>
			</p>
		</form>

		</div>
<?php
	}
	
	protected $sub_sections = Array();

	abstract public function create_db();
	abstract public function insert_db();
	//abstract public function select_db($resume_id);
	abstract public function format_entry_xhtml($val, $key);
	abstract public function format_wp_form($prev, $next);

	public function format_wp_xhtml($resume_id, $data) {
		if($this->has_entries($resume_id)) {
			$data = $this->select_db($resume_id);
		} else if (!$data && !is_array($data)) {
			return "";
		}
		
		$output = "<h" . ($this->nest_level + RESUME_EXT_NEST_OFFSET) . ">" . $this->title . "</h" . ($this->nest_level + RESUME_EXT_NEST_OFFSET) . ">" . "<ul>";

		foreach($data as $key => $val) {
			$output .= $this->format_entry_xhtml($val, $key);
		}

		return $output . "</ul>";

	}

	public function format_wp_admin_xhtml() {
		session_start();
		return $this->format_wp_xhtml(NULL, $_SESSION['resume'][$this->id]);
	}

	public function add_data() {
		session_start();
		$_SESSION['resume'][$this->id][] = $this->filter_data();
	}

	public function get_title() {
		return $this->title;
	}
	public function get_cta() {
		return $this->cta;
	}
	public function get_id() {
		return $this->id;
	}
	
	/**
	 * The fallback get data function
	 *
	 * @since 0.3
	 * @access public
	 * @return array an associative array of data about the section strong, title, desc
	 */
	public function select_db_fallback($resume_id) {
		return Array(
			"strong" => NULL,
			"title" => NULL,
			"desc" => NULL
		);
	}
	
	/**
	 * Get the list of resumes stored by the plugin
	 * 
	 * @todo move this to its own object
	 * @since 0.3
	 * @access public
	 * @return array an associative array of resumes. id => title
	 */
	public function get_resumes() {
		global $wpdb;
		return ($wpdb->get_results("select resume_id, title from " . resume_ext_db_manager::make_name(resume_ext_db_manager::name_resume), ARRAY_A ));
	}

	public function has_entries($id) {
		global $wpdb;
		return ($wpdb->get_var("select count(*) from $this->count_table where $this->count_table_id = $id") > 0);
	}

	public function __construct($index) {
		$this->index = $index;
		$this->id = $this->id . "_" . $index;
		$this->count_table = resume_ext_db_manager::make_name($this->count_table);
	}

}
?>
