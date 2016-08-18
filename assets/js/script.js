jQuery(document).ready(function ($) {
	$('.comment-search-form').submit(function (e) {
		e.preventDefault()
		var comment_id = $('#comment-id', this).val();
		var data = {
			action: 'generate_comment',
			comment_id : comment_id
		};
		jQuery.post(ajaxurl, data, function(response) {
			$('#show_comments').html(response);
		});
	});
});