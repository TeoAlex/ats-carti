jQuery(document).ready(function () {
	jQuery('#lista').click(function (event) {
		event.preventDefault();
		jQuery('#carti .item').addClass('list-group-item');
		jQuery('#carti .item').removeClass('grid-group-item');
		jQuery('#lista, #grila').toggleClass('active');
	});
	jQuery('#grila').click(function (event) {
		event.preventDefault();
		jQuery('#carti .item').removeClass('list-group-item');
		jQuery('#carti .item').addClass('grid-group-item');
		jQuery('#lista, #grila').toggleClass('active');
	});

	jQuery('#gen_select, #author_select').on('change', function () {
	    jQuery('#no_books').css('display','block');
		var term_gen = jQuery('#gen_select').val();
		var term_aut = jQuery('#author_select').val();
		jQuery('.ats-book-container').each(function () {
			var book_terms = jQuery(this).data('gender');
			var book_authors = jQuery(this).data('author');
			book_terms = book_terms.split(',');
			if (
			    ((jQuery.inArray(term_gen, book_terms) !== -1) && (book_authors == term_aut)) ||
			    ((jQuery.inArray(term_gen, book_terms) !== -1) && (term_aut == 'toate')) ||
			    ((term_gen == 'toate') && (book_authors == term_aut)) ||
			    ((term_gen == 'toate') && (term_aut == 'toate'))
			   ) {
				jQuery(this).css('display', 'block');
				jQuery('#no_books').css('display','none');
			} else {
				jQuery(this).css('display', 'none');
			}
		});

	});
});
		
		


