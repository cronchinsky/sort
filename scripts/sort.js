$(function() {

    // Directions box
    $directionsBox = $('.sort-directions-box');
    $directionsBox.find('.sort-directions-content').hide();
    $directionsBox.find('legend').click(function () {
       $this = $(this);
       if ($this.find('.ui-icon').hasClass('ui-icon-triangle-1-e')) {
           $this.find('.ui-icon').removeClass('ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-s');
           $this.parent().find('.sort-directions-content').show(400);
           $this.closest('.sort-directions-box').addClass('sort-directions-visible').animate({'padding':'15px'},400);
       }
       else {
           $this.find('.ui-icon').removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e');
           $this.parent().find('.sort-directions-content').hide(400);
           $this.closest('.sort-directions-box').removeClass('sort-directions-visible').animate({'padding':0},400);
       }
    });
    
    // Buttons that fade in when hovered.
    $('.sort-studentwork a.ui-icon').css({'opacity':.7}).hover( function () {
        $(this).stop(false,false).animate({'opacity':1},100);
    }, function () {
        $(this).stop(false,false).animate({'opacity':.7},100);
    });
    
    
    // there's the gallery and the categories
    var $gallery = $( "#sort-gallery" );
    $categories = $( ".sort-category" );

    // let the gallery items be draggable
    $( '.sort-draggable' ).draggable({
        cancel: "a.ui-icon", // clicking an icon won't initiate dragging
        revert: "invalid", // when not dropped, the item will revert back to its initial position
        containment: ".sort-drag-interface",
        cursorAt: {left: 0, top: 0},
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
        }
    });

    // let the gallery be droppable as well, accepting items from the categories
    $gallery.droppable({
        accept: ".sort-category li",
        activeClass: "custom-state-active",
        drop: function( event, ui ) {
            uncategorize( ui.draggable );
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
            });
        });
        var swid = $item.attr('id').split('_').pop();
        var value = $category.attr('id').split('_').pop();
        $('[name=studentwork_classify_' + swid +']').val(value);

    }

    // image uncategorize function
    function uncategorize( $item ) {
        $item.fadeOut(function() {
            $item
            .removeAttr('style')
            .find( "img" )
            .end()
            .appendTo( $gallery )
            .fadeIn();

            var swid = $item.attr('id').split('_').pop();
            $('[name=studentwork_classify_' + swid +']').val(0);
        });
    }

    // image preview function, demonstrating the ui.dialog used as a modal window
    function viewLargerImage( $link ) {
        var src = $link.attr( "href" ),
        title = $link.siblings( "img" ).attr( "alt" ),
        $modal = $( "img[src$='" + src + "']" ).find('.sort-modal-image');

        if ( $modal.length ) {
            $modal.dialog( "open" );
        } else {
            var img = $( "<img class='sort-modal-image' alt='" + title + "' style='display: none; padding: 8px;' />" )
            .attr( "src", src ).appendTo( "body" );
            img.load( function () {
                $(this).dialog({
                    title: $(this).attr('alt'),
                    width: $(this).width()+16,
                    modal: true
                });
            });


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
});
