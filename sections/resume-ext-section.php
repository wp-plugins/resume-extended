<?php
abstract class resume_ext_section {
	protected $title = "Generic Un-named section";
	protected $cta = "Add Generic";
	protected $id = 'generic';

	protected $wp_action = "resume_new";

	protected $index = 0;

	protected $filters = FILTER_SANITIZE_SPECIAL_CHARS;
	protected function filter_data() {
		return filter_input_array(INPUT_POST, $this->filters);
	}

	protected function format_dl_item($strong, $title, $desc)  {
		return "<dt class=\"part_title\"> " . (($strong)? "<strong>" . $strong . "</strong> ": "") . $title . "</dt>"
			. "<dd class=\"part_desc\">" . $desc . "</dd>";
	}

	protected function format_start_form() {
		global $resume_ajax; ?>
		<div class="sub_form">
		<div id="resume_<?= $this->id ?>_target">
		</div>
		<form action="<?= $resume_ajax ?>" method="post" id="resume_<?= $this->id ?>">
			<input type="hidden" name="action" value="<?= $this->wp_action ?>" />
			<input type="hidden" name="sub_action" value="<?= $this->index ?>" />
<?
	}

	protected function format_end_form($prev, $next) {?>
		<script type="text/javascript">(function($) {
			$(document).ready(function () {
		<? if($next) { ?>
				$("#next_page_<?= $next->get_id() ?>").click(function() {
					$("#tabs").tabs('select', '#tab-<?= $next->get_id() ?>');
				});
		<? }

		if($prev) { ?>
				$("#prev_page_<?= $prev->get_id() ?>").click(function() {
					$("#tabs").tabs('select', '#tab-<?= $prev->get_id() ?>');
				});
		<? } ?>
			});

			})(jQuery);</script>
			<p class="submit">
		<? if($prev) { ?>
				<input type="button" id="prev_page_<?= $prev->get_id() ?>" value="&laquo;"/>
		<? } ?>
				<input type="submit" id="ajax_action" value="<?= $this->cta ?>" />

		<? if($next) { ?>
				<input type="button" id="next_page_<?= $next->get_id() ?>" class="button-primary" value="<?= $next->get_title() ?> &raquo;"/>
		<? } ?>
			</p>
		</form>

		</div>
<?
	}

	abstract public function create_db();
	abstract public function insert_db();
	abstract public function format_entry_xhtml($val, $key);
	abstract public function format_wp_form($prev, $next);

	public function format_wp_xhtml() {
		$output = "<h3>" . $this->title . "</h3>" . "<dl>";

		session_start();

		if($_SESSION['resume'][$this->id]) {
			foreach($_SESSION['resume'][$this->id] as $key => $val) {
				$output .= $this->format_entry_xhtml($val, $key);
			}
		}

		return $output . "</dl>";
	}

	public function format_wp_admin_xhtml() {
		return $this->format_wp_xhtml();
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

	public function __construct($index) {
		$this->index = $index;
		$this->id = $this->id . "_" . $index;
	}

}
?>