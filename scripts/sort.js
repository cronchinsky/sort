$(function() {
    
    // Turns off funky refresh behavior in firefox.  Hidden form holds
    // data from before refresh.
    if($.browser.mozilla) $("form").attr("autocomplete", "off");
    
    // Directions box
    $directionsBox = $('.sort-directions-box');
    $directionsBox.find('.sort-directions-content').hide();
    $directionsBox.find('legend').click(function () {
        $this = $(this);
        if ($this.find('.ui-icon').hasClass('ui-icon-triangle-1-e')) {
            $this.find('.ui-icon').removeClass('ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-s');
            $this.parent().find('.sort-directions-content').show(400);
            $this.closest('.sort-directions-box').addClass('sort-directions-visible').animate({
                'padding':'15px'
            },400);
        }
        else {
            $this.find('.ui-icon').removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e');
            $this.parent().find('.sort-directions-content').hide(400);
            $this.closest('.sort-directions-box').removeClass('sort-directions-visible').animate({
                'padding':0
            },400);
        }
    });
    
    // Buttons that fade in when hovered.
    $('.sort-studentwork a.ui-icon').css({
        'opacity':.7
    }).hover( function () {
        $(this).stop(false,false).animate({
            'opacity':1
        },100);
    }, function () {
        $(this).stop(false,false).animate({
            'opacity':.7
        },100);
    });
    
    
    // Examples Accordion
    $('#accordion').accordion({
        collapsible:true, 
        active:false, 
        autoHeight:false
    });
    
    // there's the gallery and the categories
    var $gallery = $( "#sort-gallery" );
    $categories = $( ".sort-category" );

    // let the gallery items be draggable
    $( '.sort-draggable' ).draggable({
        cancel: "a.ui-icon,input", // clicking an icon won't initiate dragging
        revert: "invalid", // when not dropped, the item will revert back to its initial position
        containment: ".sort-drag-interface",
        cursorAt: {
            left: 0, 
            top: 0
        },
        helper: function () {
            $dragger = $(this).clone();
            $dragger.css({
                'width' : '48px',
                'height' : '3em'
            })
            .find('img').remove();
            
            $dragger.find('.ui-icon').remove();
            return $dragger;
        },
        cursor: "move"
    });

    // let the categories be droppable, accepting the gallery items
    $categories.droppable({
        accept: "#sort-gallery > li, .sort-category li",
        activeClass: "ui-state-highlight",
        drop: function( event, ui ) {
            categorize( ui.draggable, $(this) );
            ui.draggable.children("label").hide();
            ui.draggable.children("input").hide();
            ui.draggable.children("p").hide();
						//ui.draggable.children(".content").show();
            sortUpdateCorrect();
        }
    });

    // let the gallery be droppable as well, accepting items from the categories
    $gallery.droppable({
        accept: ".sort-category li",
        activeClass: "custom-state-active",
        drop: function( event, ui ) {
            uncategorize( ui.draggable );
            ui.draggable.children("label").show();
            ui.draggable.children("input").show();
            ui.draggable.children("p").show();
            sortUpdateCorrect();

        }
    });
    
    // image categorize function
    function categorize( $item, $category ) {
        $item.fadeOut(function() {
            var $list = $( "ul", $category ).length ?
            $( "ul", $category ) :
            $( "<ul class='sort-gallery ui-helper-reset'/>" ).appendTo( $category );
            $item.appendTo($($category).find('ul')).fadeIn( function () {
                $(this)
                .animate({
                    width: "48px",
                    height: "3em"
                });
                sortUpdateCorrect();
            });
        });
        console.log($item);
        var itemId = $item.attr('id');
        var swid = $item.attr('id').split('_').pop();
        var value = $category.attr('id').split('_').pop();
        var comment = $('#comment_' + swid).val();
        console.log(itemId);   
        console.log(comment);    
        console.log(swid);
        $('[name=studentwork_classify_' + swid +']').val(value);
        $('[name=studentwork_comment_' + swid +']').val(comment);
        $('[name=submitbutton]').addClass('needs-save');
    }

    // image uncategorize function
    function uncategorize( $item ) {
        $item.fadeOut(function() {
            $item
            .removeAttr('style')
            .find( "img" )
            .end()
            .appendTo( $gallery )
            .fadeIn( function () {
                sortUpdateCorrect();
            });

            var swid = $item.attr('id').split('_').pop();
            $('[name=studentwork_classify_' + swid +']').val(0);
            $('[name=submitbutton]').addClass('needs-save');
            
        });
    }

    // image preview function, demonstrating the ui.dialog used as a modal window
    function viewLargerImage( $link ) {
        var src = $link.attr( "href" ),
        title = $link.siblings( "img" ).attr( "alt" ),
        $modal = $( "img[src$='" + src + "']" ).find('.sort-modal-image');

        if ( $modal.length ) {
            $modal.dialog( "open" );
            $modal.dialog("option", "position", "top");
        } else {
            var img = $( "<img class='sort-modal-image' alt='" + title + "' style='display: none; padding: 8px;' />" )
            .appendTo( "body" );
            img.load( function () {
                $(this).dialog({
                    title: $(this).attr('alt'),
                    width: $(this).width()+16,
                    modal: true
                });
                $(this).dialog("option", "position", "top");
            }).attr('src',src);


        }
    }
    
    function nextImage($arg) {
        $studentwork = $arg.closest('.sort-studentwork');
        $gallery = $('#sort-gallery');
        $end_message = $('.sort-gallery-end-message');
        
        $studentwork.fadeOut(function () {
            $(this).prependTo($gallery);
            $end_message.prependTo($gallery);
            
            $(this).delay(500).fadeIn();
        });
    }

    // resolve the icons behavior with event delegation
    $( ".sort-draggable" ).click(function( event ) {
        var $item = $( this ),
        $target = $( event.target );

        if ( $target.is( "a.ui-icon-magnifying" ) ) {
            viewLargerImage( $target );
        }
        else if ($target.is("a.sort-next-button")) {
            nextImage($target);
        }
        else if ($target.is("ul.sort-actions-list a")) {
            return true;
        }

        return false;
    });
    
    $('[name=submitbutton]').click(function () {
        $(this).removeClass('needs-save');
    });
    
    $('.sort-show-correct-link').click(function () {
       if($(this).hasClass('sort-show-correct-show')) {
           sortShowCorrect();
           $(this).addClass('sort-show-correct-hide')
                  .removeClass('sort-show-correct-show')
                  .text('Stop Checking Work');
                      $('#correct-key').show(300);
       }
       else {
           sortHideCorrect();
           $(this).addClass('sort-show-correct-show')
                  .removeClass('sort-show-correct-hide')
                  .text('Check Work');
                  $('#correct-key').hide(300);
       }
       return false;
    });
});

window.onbeforeunload = function (e) {
    if ($('#id_submitbutton').hasClass('needs-save')) {
        var message = "You haven't saved your work yet.  Click OK to navigate away from this page and lose any unsaved data.";
        var e = e || window.event;
        // For IE and Firefox
        if (e) {
            e.returnValue = message;
        }

        // For Safari
        return message;   
    }
}

function sortShowCorrect() {
    
    $('.sort-category .sort-studentwork').each(function () {
       var cat_id = $(this).closest('.sort-category').attr('id').split('_').pop();
       if ($(this).attr('data-correct') == cat_id || $(this).attr('data-correct') == "0") {
           $(this).addClass('sort-is-correct');
       }
       else $(this).addClass('sort-is-not-correct');
    });
    
    $('#correct-key').addClass('sort-is-correct').show(300);
}

function sortHideCorrect() {
    $('.sort-is-correct').removeClass('sort-is-correct');
    $('.sort-is-not-correct').removeClass('sort-is-not-correct');
}

function sortUpdateCorrect() {
    sortHideCorrect();
    if ($('.sort-show-correct-link').hasClass('sort-show-correct-hide')) {
        sortShowCorrect();
    }
}
