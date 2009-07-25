<?php
abstract class resume_ext_section {
	const title = "Generic Un-named section";
	const cta = "Add " . self::title;
	const id = 'generic';

	private $filters = FILTER_SANITIZE_SPECIAL_CHARS;
	private function filter_data() {
		return filter_input_array(INPUT_POST, $filters);
	}

	private function format_dl_item($strong, $title, $desc)  {
		return "<dt class=\"part_title\"> " . (($strong)? "<strong>" . $strong . "</strong> ": "") . $title . "</dt>"
			. "<dd class=\"part_desc\">" . $desc . "</dd>";
	}

	private function format_start_form() {
		global $resume_ajax; ?>
		<div class="sub_form">
		<div id="resume_<?= self::title ?>_target">
		</div>
		<form action="<?= $resume_ajax ?>" method="post" id="resume_<?= self::title ?>">
			<input type="hidden" name="action" value="resume_new" />
			<input type="hidden" name="sub_action" value="<?= self::title ?>" />
<?
	}

	private function format_end_form($prev, $next) {?>
		<script type="text/javascript">(function($) {

			$(document).ready(function () {
				$("#next_page_<?= $next->id ?>").click(function() {
					$("#tabs").tabs('select', '#tab-<?= $next->id ?>');
				});
		<?	if($prev) { ?>
				$("#prev_page_<?= $prev->id ?>").click(function() {
					$("#tabs").tabs('select', '#tab-<?= $prev->id ?>');
				});
		<? } ?>
			});

			})(jQuery);</script>
			<p class="submit">
		<? if($prev) { ?>
				<input type="button" id="prev_page_<?= $prev->id ?>" value="&laquo;"/>
		<? } ?>
				<input type="submit" id="ajax_action" value="<?= self::cta ?>" />
				<input type="button" id="next_page_<?= $next->id ?>" class="button-primary" value="<?= $resume_titles[$next]?> &raquo;"/>
			</p>
		</form>

		</div>
<?
	}

	public function create_db();
	public function format_wp_xhtml();
	public function format_wp_form($prev, $next);
	public function add_data() {
		start_session();
		$_SESSION['resume'][self::id][] = filter_data();
	}

}
?>