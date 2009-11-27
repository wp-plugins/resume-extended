<?php
interface resume_ext_exportable {
	function select_db($resume_id);
	function select_db_fallback($resume_id);
}
?>
