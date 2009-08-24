(function($) {
	var disables = [];

	$(document).ready(function() {

		// do the inline labels
		$(".inline_label").each(function () {

			var label = $(this).text();
			var dirty = false;
			$("#" + $(this).attr("for"))
				.addClass("inline_label_inactive")
				.val(label)

				.focus(function () {
					//console.debug(dirty);
					if(!dirty) {
						$(this)
							.addClass("inline_label_active")
							.removeClass("inline_label_inactive")
							.val("");
					}
				})

				.blur(function () {
					if($(this).val() == "") {
						$(this)
							.addClass("inline_label_inactive")
							.removeClass("inline_label_active")
							.val(label);
						dirty = false;
					} else {
						dirty = true;
						//console.log("set dirty true");
					}

				});
		});

		// add the date picker
		$(".form_date").datepicker({
			changeMonth: true,
			changeYear: true
		});

		// add the tabs
		$("#tabs").tabs();

		funk = function () {
			var opt = {
				target: "#" + this.id + "_target",
				resetForm: true,
				success: function(arg) {
					//console.log(this);
					$(".sub_form form").each(funk);
					$(this).slideDown(1000);

					//temp = $(form_closure);
					//temp.resetForm();

					// hack remove asap
					$(".inline_label_inactive").focus().blur();
					$(".inline_label_active").focus().blur();
					$.each(disables, function () {
						check_check_disabler(this.a, this.b);
					});
				}
			};

			//console.debug(this, opt);

			$(this).ajaxForm(opt);
		};

		$(".sub_form form").each(funk);

		$("#resume_submit").ajaxForm(
			{
				dataType: 'json',

				success: function (data) {
					$("#resume_ext_finished").html('Your resume has been created as a draft <a href="' + data.page_id + '">Resume</a>')
				}
			}
		);

		$("#resume_reset").ajaxForm(function () {
			$(".sub_form form").resetForm();
			// hack remove asap
			$(".inline_label_inactive").focus().blur();
			$(".inline_label_active").focus().blur();
			$.each(disables, function () {
				check_check_disabler(this.a, this.b);
			});
		});

	});

	$.fn.check_disables = function ( check_disables ) {
		//console.debug(this, $(this));
		return this.each(function () {
			disables.push({a:this, b:check_disables});

			$(this).change(function () {
				check_check_disabler(this, check_disables);
			});
		});
	};

	function check_check_disabler(ele, check_disables) {
		if($(ele).is("input:checked")) {
			$(check_disables).attr("disabled", "disabled");
		} else {
			$(check_disables).removeAttr("disabled");
		}
	}

})(jQuery);