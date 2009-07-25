	<form action="<?= $resume_ajax ?>" method="POST" id="resume_reset">
		<div class="submit">
		<input type="hidden" name="action" value="resume_reset" />
		<input type="submit" value="Reset Resume" />
		</div>
	</form>
	<form action="<?= $resume_ajax ?>" method="POST" id="resume_submit">
		<div>
		<input type="hidden" name="action" value="resume_finalize" />
		<input type="submit" class="button-primary" value="Create Resume &raquo;" />
		</div>
	</form>