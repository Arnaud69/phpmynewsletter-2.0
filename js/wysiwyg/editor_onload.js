$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});
$(window).resize(function() {
    $("#downloadModal TEXTAREA").css("height", $(window).height() - 70);
    var rightmenu = $('.rightsidemenu').width() / 2;
    var rightsidemenu = rightmenu + 15;
    var previewwidth = 154 / 2;
    //$('.rightsidemenu .preview').css("marginLeft", 280);
});
$(window).ready(function() {
    $('.export_options').click(function() {
        $('.rightsidebar').css({
            right: '0px'
        });
    });
    tinymcereload();
    var rightmenu = $('.rightsidemenu').width() / 2;
    var rightsidemenu = rightmenu + 15;
    var previewwidth = 154 / 2;
    $('.rightsidemenu .preview').css("marginLeft", rightsidemenu - previewwidth);
    $('.close_export_options').click(function(e) {
        e.preventDefault();
        $('.rightsidebar').css({
            right: '-300px'
        });
    });
    $('[aria-label="Text color"]').bind('mouseenter', function() {
        $(this).find('.picker').colpick({
            layout: 'hex',
            submit: 0,
            flat: true,
            onChange: function(hsb, hex, rgb, fromSetColor) {
                //$('.picker').val('#' + hex);
            }
        });
    });
    $('.mod_thumb').tooltip();
});
$('.global_bgcolor').colpick({
    layout: 'hex',
    submit: 0,
    onChange: function(hsb, hex, rgb, fromSetColor) {
        $('[st-sortable]').attr('bgcolor', '#' + hex);
    }
});
$('.global_bgcolor').click(function() {
    $('.global_bgcolor_picker').fadeIn(500);
});
$('.global_bgcolor_picker').mouseleave(function() {
    $(this).fadeOut(500);
});
var arrayUnique = function(a) {
    return a.reduce(function(p, c) {
        if (p.indexOf(c) < 0) p.push(c);
        return p;
    }, []);
};
$(document).ready(function() {
    $('.darkoverlay_download').find('li').on('click', 'a', function(e) {
        e.preventDefault();
        var $el = $(e.currentTarget);
        saveLayoutSrc($el.data('template_id')).done(function() {
            setTimeout(function() {
                window.location = $el.attr('href');
            }, 1000);
        });
    });
    $('.emailrender a').click(function(event) {
        event.preventDefault();
    });
    $('iframe a').click(function(event) {
        event.preventDefault();
    });
    $("[st-bgcolor='edit']").prepend('<div class="colorSelectorinner"></div>');
    $("#downloadModal TEXTAREA").css("height", $(window).height() - 40);
    $('iframe').css({
        "height": $(window).height() - 120
    });
    saved_html();
    mouseover();
    move_up_down();
    upload_buttons();
    var er = $('.emailrender');
    er.find('[st-sortable]').find('table:first').prepend('<div class="innerbg"></div>');
    er.css("height", "auto");
});
$('img[st-image]').each(function(i, el) {
    var $el = $(el);
    if (!$el.attr('id')) {
        //generate random ID
        $el.attr('id', Math.random().toString(36).slice(2));
    }
});
$('img[st-image]').on('load', function(e) {
    var img = e.currentTarget;
    var $el = $(e.currentTarget);
    if ($el.parent().is('a')) {
        if (!$el.parent().parent().hasClass('imgpop')) {
            $el.parent().wrap('<div class="imgpop" />');
        }
    } else {
        if (!$el.parent().hasClass('imgpop')) {
            $el.wrap('<div class="imgpop" />');
        }
    }
    if ($el.parents('.imgpop').find('.uploader_wrap').length == 0 && img.clientWidth != 0 && img.clientHeight != 0) {
        var str = '<div class="uploader_wrap" style="width:' + img.clientWidth + 'px; margin-top:' 
            + (img.clientHeight / 2 - 20) + 'px"><div class="upload_buttons"><div class="img_link"></div><div class="img_upload"></div>'
            + '</div></div>';
        $el.parents('.imgpop').prepend(str);
    }
    $el.parents('td').mouseenter(function() {
        $(this).find('.uploader_wrap').css('opacity', '1');
    }).mouseleave(function() {
        $(this).find('.uploader_wrap').css('opacity', '0');
    });
    upload_append();
});
function upload_buttons() {
    $('[st-image]').each(function(i, el) {
        var $el = $(el);
        if ($el.parent().is('a')) {
            if (!$el.parent().parent().hasClass('imgpop')) {
                $el.parent().wrap('<div class="imgpop" />');
            }
        } else {
            if (!$el.parent().hasClass('imgpop')) {
                $el.wrap('<div class="imgpop" />');
            }
        }
        if ($el.parents('.imgpop').find('.uploader_wrap').length == 0 && el.clientWidth != 0 && el.clientHeight != 0) {
            var str = '<div class="uploader_wrap" style="width:' + el.clientWidth + 'px; margin-top:' 
            + (el.clientHeight / 2 - 20) 
            + 'px"><div class="upload_buttons"><div class="img_link">'
            + '</div><div class="img_upload"></div></div></div>';
            $el.parents('.imgpop').prepend(str);
        }
        $el.parents('td').mouseenter(function() {
            $(this).find('.uploader_wrap').css('opacity', '1');
        }).mouseleave(function() {
            $(this).find('.uploader_wrap').css('opacity', '0');
        });
    });
    upload_append();
}
///////////////////////////////
function saved_html() {
    var er = $('.emailrender');
    er.find('[st-sortable]').find('table:first').prepend('<div class="innerbg"></div><div class="addremove"><div class="drag"></div><div class="remove"></div></div>');
    er.find('[st-button]').prepend('<div class="buttonbg"></div>');
    var innertable = er.find('table:first').find('table:first').width() - 80;
    var parent = er.find('table:first').find('table:first').width();
    $('.emailrender .addremove').css({
        marginLeft: innertable
    });
    $('.emailrender .move_controls').css({
        marginLeft: parent / 2 - 61
    });
    er.find('table:first').find('td:first').mouseleave(function() {
        $(this).find('table:first').removeClass('selecthover');
    });
    er.find('[st-sortable]').wrap('<div class="view" />');
    er.find('.view').wrap('<div class="module" />');
}
////////////////////////////////
function mouseover() {
    $('.module .view').find('table:first').find('table:first').find('.addremove').remove();
    $('.module .view').find('table:first').find('table:first').prepend('<div class="addremove"><div class="drag"></div><div class="remove"></div></div>');
    $('.module .view').find('table:first').find('table:first').find('.innerbg').remove();
    $('.modules_sidebar').find('[st-sortable]').find('table:first').prepend('<div class="innerbg"></div>');
    $('.modules_sidebar').find('[st-button]').prepend('<div class="buttonbg"></div>');
    var innertable = $('.module .view').find('table:first').find('table:first').width();/* - 80;*/
    var parent = $('.module .view').find('table:first').find('table:first').width();
    $('.addremove').css({
        marginLeft: innertable
    });
    $('.move_controls').css({
        marginLeft: parent / 2 - 61
    });
}
$('.mce-colorbutton').bind('click', function() {});
function tinymcereload() {
    tinymce.init({
        selector: ".emailrender [st-content]",
        inline: true,
        resize: false,
        object_resizing: false,
        plugins: ["advlist link image textcolor", "media"],
        toolbar: " undo | redo | fontsizeselect | bold | italic | underline | alignleft aligncenter alignright | link | forecolor",
        menubar: false,
        toolbar_items_size: 'medium',
        force_hex_style_colors: true,
        image_advtab: true,
        extended_valid_elements: 'span[st-webversion|st-unsubscribe|style], td[st-webversion|st-unsubscribe|style], table[st-webversion|st-unsubscribe|style]'
    });
    tinymce.init({
        selector: ".emailrender [st-title]",
        inline: true,
        resize: false,
        object_resizing: false,
        plugins: [
            "link image lists",
            "filemanager textcolor"
        ],
        toolbar: " undo | redo | fontsizeselect | bold | italic | underline | alignleft aligncenter alignright | link | forecolor",
        menubar: false,
        toolbar_items_size: 'medium',
        force_hex_style_colors: true,
        cleanup: true,
        'formats': {
            'alignleft': {
                'selector': 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img, td',
                attributes: {
                    "align": 'left'
                }
            },
            'aligncenter': {
                'selector': 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img, td',
                attributes: {
                    "align": 'center'
                }
            },
            'alignright': {
                'selector': 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img, td',
                attributes: {
                    "align": 'right'
                }
            },
            'alignfull': {
                'selector': 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img, td',
                attributes: {
                    "align": 'justify'
                }
            }
        }
    });
}
$(document).ready(function() {
    $('#download_btn').click(function() {
        $('.darkoverlay_download').fadeIn(500);
    });
    $(' .alertclose').click(function() {
        $(this).parents('.darkoverlay_download').fadeOut(500);
    });
    $('.alert_close').click(function() {
        $(this).parents('.darkoverlay_download').fadeOut(500);
    });
    $('#testmail_btn').click(function() {
        $('.darkoverlay_testmail').fadeIn(500);
    });
    $(' .testmailclose').click(function() {
        $(this).parents('.darkoverlay_testmail').fadeOut(500);
    });
    $('.testmail_close').click(function() {
        $(this).parents('.darkoverlay_testmail').fadeOut(500);
    });
    $('.export-button').click(function() {
            $('.export-button').removeClass('mc_select');
            $(this).addClass('mc_select');
            $('.export-box').fadeOut(500);
            $('#' + $(this).data('provider') + '_optionsbox').fadeIn(500);
        })
        .each(function(i, el) {
            var $el = $(el);
            var position = $el.position();
            $('#' + $el.data('provider') + '_optionsbox').css({
                'left': position.left + 260,
                'top': position.top
            });
        });
    $('.module_sidebar_wrap').scroll(function() {
        $('.export-button').each(function(i, el) {
            var $el = $(el);
            var position = $el.position();
            $('#' + $el.data('provider') + '_optionsbox').css({
                'left': position.left + 260,
                'top': position.top
            });
        });
    });
    $('.close').click(function(e) {
        e.preventDefault();
        $('.export-button').removeClass('mc_select');
        $('.export-box').fadeOut(500);
    });
    ////GLOBAL BG COLOR CHANGER
    $('.innerbg').bind('mouseover', function() {
        var seldiv = $(this).parents('[st-sortable]');
        $(this).colpick({
            //flat:true,
            layout: 'hex',
            submit: 0,
            onChange: function(hsb, hex, rgb, fromSetColor) {
                $(seldiv).find('.devicewidth, .container').attr('bgcolor', '#' + hex);
            }
        });
    });
    ////BUTTON BG COLOR CHANGER
    $(document).on('mouseover', '.buttonbg', function() {
        var seldiv = $(this).parent('[st-button]');
        $(this).colpick({
            //flat:true,
            layout: 'hex',
            submit: 0,
            onChange: function(hsb, hex, rgb, fromSetColor) {
                seldiv.attr('bgcolor', '#' + hex);
                seldiv.css('background-color', '#' + hex);
            }
        });
    });
    ////TEXT COLOR CHANGER
    $(document).on('mouseover', '.picker', function() {
        var seldiv = $(this).parent('[st-button]');
        $(this).colpick({
            //flat:true,
            layout: 'hex',
            submit: 0,
            onChange: function(hsb, hex, rgb, fromSetColor) {
                $(seldiv).val('#' + hex);
            }
        });
    });
});
$('#myonoffswitch').change(function() {
    if ($('#myonoffswitch').is(':checked')) {
        $('.mobileoverlay').fadeOut();
        $('body').css({
            'overflow': ''
        });
    } else {
        $('.mobileoverlay').fadeIn();
        preview_mobile();
        $('body').css({
            'overflow': 'hidden'
        });
        var $content = $("iframe.editframe").contents();
        $content.find("body").find('a').click(function(e) {
            e.preventDefault();
        });
    }
});
function upload_append() {
    $(document).on('click', '.img_upload', function() {
        $('.darkoverlay_imgupload').remove();
        $(this).parents(".imgpop").append('<div class="darkoverlay_imgupload">' +
            '<div class="imageuploader">' +
            '<header><h3>Upload Image</h3></header>' +
            '<div class="imgupload_form">' +
            '<form action="ajax/upload.php" method="post" enctype="multipart/form-data" id="UploadForm">' +
            '<div class="fileUpload"><i class="glyphicon glyphicon-upload"></i> <span>Choose file</span>' +
            '<input id="uploadFile" placeholder="Choose File" disabled="disabled" />' +
            '<input id="uploadBtn" name="ImageFile" required type="file" class="upload" />' +
            '<input id="imgwidth" name="imgwidth" type="hidden" class="upload" />' +
            '<input id="imgheight" name="imgheight" type="hidden" class="upload" />' +
            //'<input id="templateEditId" name="templateEditId" type="hidden" class="upload" value="' + $('a.html_download').data('template_id') + '" />' +
            '</div>' +
            '<div class="imgupload_ctrls">' +
            '<a class="close_upload" href="#">Close</a>' +
            '<input type="submit"  id="save_upload" value="Upload" />' +
            '</div>' +
            '</form>' +
            '<div id="output" style="display:none;"></div>' +
            '</div>' +
            '</div>' +
            '</div>');
        var imgwidth = $(this).parents('.imgpop').find('[st-image]').attr('width');
        var imgheight = $(this).parents('.imgpop').find('[st-image]').attr('height');
        $('#imgwidth').val(imgwidth);
        $('#imgheight').val(imgheight);
        $(".close_upload").bind('click', function(e) {
            e.preventDefault();
            $(this).parents(".darkoverlay_imgupload").remove();
        });
    });
    $(document).on('change', '#uploadBtn', function(e) {
        var $el = $(e.currentTarget);
        $('.fileUpload').find('span').text($el.val().replace('C:\\fakepath\\', ''));
    });
}
function img_link() {
    $(document).on('keyup', '#custom-color', function(e) {
        $('#user-color').css('color', '#' + $(this).val())
            .attr('data-mce-color', $(this).val());
        if (e.keyCode == 13) {
            $("#user-color").click();
        }
    });
    $(document).on('click', '.img_link', function() {
        var link = $(this).parents(".imgpop").find('a').attr('href');
        if (typeof link === 'undefined') {
            link = '';
        }
        $(this).parents(".imgpop").append('<div class="darkoverlay_imglink">' +
            '<div class="imageuploader">' +
            '<header><h3>Image Link</h3></header>' +
            '<div class="imgupload_form">' +
            '<input id="img_link" type="text" value="' + link + '" />' +
            '<div class="imgupload_ctrls">' +
            '<a class="close_link" href="#">Close</a>' +
            '<a class="save_link" href="#">Save</a>' +
            '</div>' +
            '</div>' +
            '</div>');
        $(".close_link").bind('click', function(e) {
            e.preventDefault();
            $(this).parents(".darkoverlay_imglink").remove();
        });
        $(".save_link").bind('click', function() {
            var link = $(this).parents(".imageuploader").find('#img_link').val();
            if ($(this).parents('.imgpop').find('img').parent("a").length) {
                $(this).parents('.imgpop').find('img').parent("a").attr('href', link);
                $(this).parents(".darkoverlay_imglink").remove();
            } else {
                $(this).parents('.imgpop').find('img').wrap('<a href=' + link + '>');
                $(this).parents(".darkoverlay_imglink").remove();
            }
        });
    });
}
$(document).ready(function() {
    upload_append();
    img_link();
    $('.no-provider').on('click', function(e) {
        window.location = e.currentTarget.getAttribute('data-href');
    })
});
$(document).on('submit', '#UploadForm', function(e) {
    e.preventDefault();
    $('.darkoverlay_imgupload').css({
        'display': 'none'
    });
    $("#save_upload").attr("disabled", "");
    $(this).parents('.imgpop').find(".uploader_wrap").after("<span style='background:#ffffff;border-radius:3px;position:absolute;'><img src='../css/imgloader.gif'></span>");
    $(this).ajaxSubmit({
        target: "#output",
        success: afterSuccess
    });
});
function afterSuccess() {
    var output = $('#output');
    var imgurl = output.text();
    //console.log('success', imgurl);
    var $img = output.parents('.imgpop').find('[st-image]');
    $img.attr('src', imgurl);
    $(".imgpop").find('span').remove();
    $("#UploadForm").resetForm();
    $("#save_upload").removeAttr("disabled");
    output.parents(".darkoverlay_imgupload").remove();
}


