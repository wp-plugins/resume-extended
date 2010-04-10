<?php
/**
 * Manage the exporting of resumes
 *
 * list and apply themes to the resume
 *
 * @package resume-extended
 * @since 0.3
 */
class resume_ext_export {
	protected $path = "";
	protected $format_for_page = null;
	protected $format_list = array();
	protected $mime_list = array();
	protected $title_list = array();
	
	/**
	 * List of callbacks
	 *
	 * @access protected
	 * @since 0.3
	 */
	protected $section_list = array();
	
	/**
	 * add a format to the list of available formats
	 *
	 * @access protected
	 * @since 0.3
	 */
	protected function add_format($format_id, $file, $title, $mime = "text/html") {
		$this->title_list[$format_id] = $title;
		$this->format_list[$format_id] = $file;
		$this->mime_list[$format_id] = $mime;
	} 
	
	/**
	 * add a section to a format
	 *
	 * @access protected
	 * @since 0.3
	 */
	 
	protected function add_section($format_id, $section_type, $callback) {
		$this->section_list[$format_id][$section_type] = $callback;
	}
	
	/**
	 * set the format used for the wordpress page
	 *
	 * @access protected
	 * @since 0.3
	 */
	 
	protected function use_for_wp($format_id) {
		$this->format_for_page = $format_id;
	}
	
	/**
	 * get the format used for the wordpress page
	 *
	 * @access public
	 * @since 0.3
	 */
	 
	function get_for_wp() {
		return $this->format_for_page;
	}
	
	/**
	 * get the mime-type associated with a format
	 *
	 * @access public
	 * @return the mime-type
	 * @since 0.3
	 */
	function get_mime_type($format_id) {
		//var_dump($mime_list);
		return $this->mime_list[$format_id];
	}
	
	/**
	 * list all the fromats supported by the current theme
	 *
	 * @access public
	 * @return array an array of formats
	 * @since 0.3
	 */
	function list_formats() {
		return $this->title_list;
	}
	
	/**
	 * apply the selected format
	 *
	 * apply the selected format and echo it.
	 *
 	 * @param $format_id the format id
 	 * @param $sections the sections array
	 * @access public
	 * @since 0.3
	 */
	function apply_format($resume_id, $format_id, $sections) {
		//var_dump($sections);
		global $resume_section_lookup;
		if(is_file($this->path . $this->format_list[$format_id])) {
		
			include_once($this->path . $this->format_list[$format_id]);
			
			foreach($sections as $title => $sect) {
				//var_dump($title, $this->section_list[$format_id][$resume_section_lookup[$title]]);
				if($sect->has_entries($resume_id)) {
					if(isset($this->section_list[$format_id][$resume_section_lookup[$title]]) && function_exists($this->section_list[$format_id][$resume_section_lookup[$title]])) {
						$data = $sect->select_db($resume_id);
						if(is_array($data)) {
							call_user_func($this->section_list[$format_id][$resume_section_lookup[$title]], $sect->get_title(), $data);
						}
					} else if (isset($this->section_list[$format_id]["fallback"]) && function_exists($this->section_list[$format_id]["fallback"])) {
						$data = $sect->select_db_fallback($resume_id);
						if(is_array($data)) {
							call_user_func($this->section_list[$format_id]["fallback"], $sect->get_title(), $data);
						}
					}
				}
			}
		}
	}
	
	/**
	 * construct the object
	 *
	 * @access public
	 * @since 0.3
	 */
	function __construct() {
		$this->path = ((!is_dir(RESUME_EXT_EXTERNAL_THEME_PATH))? RESUME_EXT_THEME_PATH : RESUME_EXT_EXTERNAL_THEME_PATH);
		
		if(is_file($this->path . "theme-register.php")) {
			$resume =& $this;
			include_once($this->path . "theme-register.php");
		}
	}
}
?>
