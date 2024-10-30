jQuery(document).ready(function($){
    
    // Adapt the image in the lightbox to the viewport.
    function image_resizer() {
        $theImage = $('.lri-main-content img');
        var imgWidth = $theImage.width();
        var imgHeight = $theImage.height();
        var imgRatio = imgWidth / imgHeight;
        var resize = false;
        var forceResize = meowapps_lri.force_resize;
        if(imgHeight > $theImage.parent().height() || imgWidth  > $theImage.parent().width() || forceResize) {
            resize = true;
        }
        if(resize) {
            if(imgWidth > imgHeight) {
                // Landscape
                $theImage.css('width', '100%');
                var oldWidth = 100;
                // We progressively reduce the image width to fit the div.
                while($theImage.height() > $theImage.parent().height()) {
                    $theImage.css('width', (oldWidth-5)+"%");
                    oldWidth -= 5;
                }
            }
            if(imgWidth < imgHeight) {
                // Portrait
                $theImage.css('height', '100%');
                var oldHeight = 100;
                // We progressively reduce the image height to fit the div.
                while($theImage.width() >= $theImage.parent().width()) {
                    $theImage.css('height', (oldHeight-5)+"%");
                    oldHeight -= 5;
                }
            }   
        }
        // Center the resized image Verticaly
        var finalImgHeight = $theImage.height();
        var parentHeight = $theImage.parent().height();
        var dif = parentHeight - finalImgHeight;
        $theImage.css('margin-top', dif/2);
    }

    function toggle_lightbox(index, effect) {
        var $loader = $('.lri-loader');
        $loader.show();
        var current_index = index;
        var img_tag = images_list[current_index];
        display_lightbox(img_tag, current_index, meowapps_lri.theme);
        var $lriMainContent = $('.lri-main-content');
        $lriMainContent.css('opacity', 0); // For firefox who would show the image before it has been resized.
        $lriMainContent.imagesLoaded( function() {
            $loader.hide();
            if(effect && meowapps_lri.slide_effect == true) {
                image_resizer();
                $lriMainContent.css('opacity', 1);
                $lriMainContent.children('img').addClass('animated '+effect);
            }
            else {
                image_resizer();
                $lriMainContent.css('opacity', 1);
                $lriMainContent.children('img').addClass('animated '+meowapps_lri.image_effect);
            }
        });
    }

    function display_lightbox(img_tag, current_index, theme) {
        var next_index = get_next_index(current_index);
        var prev_index = get_prev_index(current_index);

        // Adding the active class to the lightbox div
        $('.lri-main').addClass('active');

        // Find the id
        var htmlTag = $.parseHTML(img_tag);
        var image_id = $(htmlTag).attr('lri-image-id');

        // Appending the lightbox div content
        if (!image_id || theme == "plain") {
            $('.lri-main').addClass('theme-classic');
            var content =
            "<div class='lri-main-content'>"
                    +img_tag+
            "</div> \
            <a class='lri-nav-link lri-prev-link' lri-nav-index='"+prev_index+"'><i class='fa fa-angle-left'></i></a> \
            <a class='lri-nav-link lri-next-link' lri-nav-index='"+next_index+"'><i class='fa fa-angle-right'></i></a> \
            <div class='lri-loader'><img src='" + meowapps_lri.plugin_url + "svg-loaders/rings-white.svg' width='60' alt='Loading'></div>";
            $('.lri-main').html(content);
        }
        else if (theme == "shibuya") {
            $('.lri-main').addClass('theme-shibuya');
            var data = {
              'action': 'get_attachment_meta',
              'img_id': image_id
            };
            var content =
            "<div class='lri-main-content'> \
                <div class='lri-img-preview'>"
                    +img_tag+
                    "<a class='lri-nav-link lri-prev-link' lri-nav-index='"+prev_index+"'><i class='fa fa-angle-left'></i></a> \
                    <a class='lri-nav-link lri-next-link' lri-nav-index='"+next_index+"'><i class='fa fa-angle-right'></i></a> \
                </div> \
                <div class='lri-img-meta'> \
                </div> \
            </div>";
            $('.lri-main').html(content);
            jQuery.post(ajax_object.ajax_url, data, function(response) {
                (response);
                var meta = "<div class='lri-img-meta-content'> \
                                <h1>"+response.title+"</h1> \
                                <p>"+response.description+"</p> \
                                <div class='preview-exif'>"
                                if(response.camera != '') {
                                    meta = meta + "<i class='icon ion-ios-camera-outline'></i> "+response.camera; 
                                }
                                if(response.focal_length != 0) {
                                    meta = meta + "<i class='icon ion-ios-eye-outline'></i> "+response.focal_length+"mm";
                                }
                                if(response.aperture != 0) { 
                                    meta = meta + "<i class='icon ion-aperture'></i> f/"+response.aperture;
                                }
                                if(response.shutter_speed != 0) {
                                    meta = meta + "<i class='ionicons ion-ios-timer-outline'></i> "+response.shutter_speed;
                                }
                                if(response.iso != 0) {
                                    meta = meta + "<i class='ionicons ion-ios-bolt-outline'></i>  "+response.iso+" ISO";
                                }
                                meta = meta + "</div> \
                            </div>";
                $('.lri-img-meta').html(meta).hide().fadeIn('fast');
            }, 'json');
        }
    }

    function get_current_index() {
        if($('.lri-main').hasClass('active')) {
            return $('.lri-item-img').attr('lri-index');
        }
        // If there is no current index being displayed
        else {
            return false;
        }
    }

    function get_next_index(current_index) {
        if (typeof current_index === 'undefined') {
            return $('.lri-next-link').attr('lri-nav-index');
        }
        // If there is no image being currently displayed
        else {
            var last_index = images_list.length - 1;
            if(current_index == last_index) {
                return next_index = 0;
            }
            else {
                return next_index = parseInt(current_index) + 1;
            }
        }
    }

    function get_prev_index(current_index) {
        if (typeof current_index === 'undefined') {
            return $('.lri-prev-link').attr('lri-nav-index');
        }
        // If there is no image being currently displayed
        else {
            var last_index = images_list.length - 1;
            if(current_index == 0) {
                return prev_index = last_index;
            }
            else {
                return prev_index = parseInt(current_index) - 1;
            }
        }
    }

    // When document is ready
    $(document).ready(function()  {
        var base_selector = meowapps_lri.css_selector;
        // Creating the main lightbox div called lri-main
        $('body').append("<div class='lri-main'></div>");
        // Adding a lri-item-link class to all links containing an img.
        $(base_selector + ' a:has(img)').addClass('lri-item-link');
        // Adding a lri-item-img class to all images contained in an lri-item-link.
        $(base_selector + ' a:has(img)').children('img:first-child').addClass('lri-item-img');
        // When clicking on a lri-item-link
        $(document).on( 'click', '.lri-item-link', function(e) {
            e.preventDefault();
            // Creating the list of all lri-item-link
            images_list = [];
            $('.lri-item-link').each(function(index) {
                var $original_image = $(this).children('img:first-child').attr('lri-index', index);
                var $image_clone = $(this).children('img:first-child').clone();
                var image_width = $original_image.width();
                var image_height = $original_image.height();
                if(image_width > image_height) {
                    var orientation = 'lri-landscape';
                }
                else {
                    var orientation = 'lri-portrait';
                }

                imageId = null;
                $.each($image_clone, function (i, v) {
                  var allClasses = v.className.split(/\s+/);
                  if ( v.attributes['aria-describedby'] ) {
                    // Image that is part of a WP gallery
                    var reg = /gallery\-[1-9]{0,10}\-([1-9]{0,10})/g;
                    var match = reg.exec(v.attributes['aria-describedby'].value);
                    if (match.length > 1) {
                      imageId = match[1];
                    }
                  }
                  else {
                    // Image that was inserted
                    for (var j in allClasses) {
                        var className = allClasses[j];
                        var found = className.match(/wp-image-[0-9]{0,100}/g);
                        if ( found && found.length > 0 ) {
                          imageId = found[0].replace( 'wp-image-', '' );
                          break;
                        }
                    }
                  }
                });

                // If no imageId, there is no information available.
                // We could display the image in full-screen and avoid to display void information.

                $(this).each(function() {
                  $.each(this.attributes, function() {
                    // this.attributes is not a plain object, but an array
                    // of attribute nodes, which contain both the name and value
                    if(this.specified) {
                      (this.name, this.value);
                    }
                  });
                });
                images_list.push(
                    $image_clone
                        .removeClass()
                        .addClass('lri-item-img')
                        .attr('lri-image-id', imageId)
                        .addClass(orientation)
                        .removeAttr('sizes style width height')
                        .attr('lri-index', index)
                        .prop('outerHTML') );
            });
            var $cliqued_image = $(this).children('img');
            var current_index = $cliqued_image.attr('lri-index');
            toggle_lightbox(current_index);
        });

        $('.lri-nav-link').on('click', function(e) {
            e.preventDefault();
        });

        // Handling differents click once the lightbox is opened.
        $('.lri-main').on('click', function(e) {
            // Except if it's a nav link
            if($(e.target).is($('.lri-nav-link')) || $(e.target).is($('i'))) {
                if($(e.target).is($('.lri-prev-link')) || $(e.target).is($('.lri-prev-link i'))) {
                    toggle_lightbox(get_prev_index(), 'slideInLeft');
                }
                if($(e.target).is($('.lri-next-link')) || $(e.target).is($('.lri-next-link i'))) {
                    toggle_lightbox(get_next_index(), 'slideInRight');
                }
            }
            else {
                $('.lri-main').removeClass('active');
            }
        });

        // Keyboard Controls
        $(document).keyup(function(e) {
            if($('.lri-main').hasClass('active')) {
                if (e.keyCode == 27) $('.lri-main').removeClass('active');  // esc
                if (e.keyCode == 39) { // right
                    toggle_lightbox(get_next_index(), 'slideInRight');
                }
                if (e.keyCode == 37) { // left
                    toggle_lightbox(get_prev_index(), 'slideInLeft');
                }
            }
        });

        // Swipe Event
        $('.lri-main').swipe( {
            //Generic swipe handler for all directions
            swipe:function(event, direction, distance, duration, fingerCount, fingerData) {
                if($('.lri-main').hasClass('active')) {
                    // If swipe left : we load the next image
                    if(direction == 'left') {
                        $('.lri-main-content').css('margin-left', distance);
                        toggle_lightbox(get_next_index(), 'slideInRight');
                    }
                    // If swipe left : we load the previous image
                    if(direction == 'right') {
                        toggle_lightbox(get_prev_index(), 'slideInLeft');
                    }
                }
            },
            triggerOnTouchEnd: true,
            allowPageScroll: "vertical",
            //Default is 75px, set to 0 so any distance triggers swipe
            threshold:75
        });
    });

    $(window).resize(function(){
        image_resizer();
    });

});
