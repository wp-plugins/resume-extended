	(function($) {
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

			$(".sub_form form").each(function () {
				var opt = {
					target: "#" + this.id + "_target",
					resetForm: true
					//success: function(arg) {
						//console.log(arg);
						//$("#" + this.id + "_target").fadeIn('slow');
					//}
				};

				//console.debug(this, opt);

				$(this).ajaxForm(opt);
			});

			$("#resume_submit").ajaxForm();

			$("#resume_reset").ajaxForm(function () {
				$(".sub_form form").resetForm();
			});

		});

		jQuery.fn.check_disables = function ( check_disables ) {
			console.debug(this, $(this));
			return this.each(function () {
				$(this).change(function () {
					if($(this).is("input:checked")) {
						$(check_disables).attr("disabled", "disabled");
					} else {
						$(check_disables).removeAttr("disabled");
					}
				});
			});
		};
	})(jQuery);