<?php
/**
 * all sections that should be exportable should implement this class
 *
 * @package resume-extended
 * @since 0.3
 * @author Aaron Spaulding
 **/
interface resume_ext_exportable {
	
	/**
	 * get the current section of resume from the DB
	 *
	 * @since 0.3
	 * @param string $resume_id the id of the resume to be retrieved
	 * @author Aaron Spaulding
	 */
	function select_db($resume_id);
	
	/**
	 * get the current section from the DB, the current theme doesn't understand this section. Use a generic structure.
	 *
	 * @param string $resume_id the id of the resume to be retrieved
	 * @author Aaron Spaulding
	 */
	function select_db_fallback($resume_id);
}
?>
