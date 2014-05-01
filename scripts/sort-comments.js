/**
 * controls the dynamic comments drop down.
 */

// When the document is ready...
$(function() {
    
    // add a 'no explanations' listing at the end of the comments
    var noComment = $("<p id='sort-comment-no-results'>* No Explanations *</p>");
    noComment.appendTo($('.sort-studentwork-comments'));
    
    // Hide the 'no explanations' text if there's comments to display
    if($('.sort-comment-box').length) noComment.hide();
    
    // When a user changes the drop down to filter comments...
    $('#sort-comment-filter').change(function () {
        // Hide the comments and the no results placeholder.
        $('.sort-comment-box').hide(100);
        $('#sort-comment-no-results').hide(100);
        var category = $(this).val();
        
        // If they've sleected a a category...
        if (category != 0) {
           // Show any comments that are for that category.  IF there are none,
           // show the no results text.
            $('.sort-comment-category-' + category).show(200);
            if ($('.sort-comment-category-' + category).length == 0) {
                $('#sort-comment-no-results').show(200);
            }
        }
        else {  // Otherwise show them all.
            if ($('.sort-comment-box').length != 0) {
                $('.sort-comment-box').show(200);
            }
            else { // unless there are none, then show the placeholder text.
                $('#sort-comment-no-results').show(200);
            }
        }
    });
    
    
    
    $('.sort-studentwork-comment-form-header').next().hide();
    $('.sort-studentwork-comment-form h3').click(function () {
       $(this).next().toggle(300).toggleClass('sort-comment-form-open');
       $(this).find('.ui-icon').toggleClass('ui-icon-triangle-1-e').toggleClass('ui-icon-triangle-1-s');
    });
});
