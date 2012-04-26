$(function() {
    
    var noComment = $("<p id='sort-comment-no-results'>* No Explanations *</p>");
    noComment.appendTo($('.sort-studentwork-comments'));
    
    if($('.sort-comment-box').length) noComment.hide();
    
    $('#sort-comment-filter').change(function () {
        $('.sort-comment-box').hide(100);
        $('#sort-comment-no-results').hide(100);
        var category = $(this).val();
        if (category != 0) {
            $('.sort-comment-category-' + category).show(200);
            if ($('.sort-comment-category-' + category).length == 0) {
                $('#sort-comment-no-results').show(200);
            }
        }
        else {
            if ($('.sort-comment-box').length != 0) {
                $('.sort-comment-box').show(200);
            }
            else {
                $('#sort-comment-no-results').show(200);
            }
        }
    });
});
