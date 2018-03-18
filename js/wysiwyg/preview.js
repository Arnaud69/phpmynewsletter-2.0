function preview_mobile(){
    $("#download-layout").empty();
    var e = "";
    var emailrendercontent=$(".emailrender").html();
    $("#download-layout").html(emailrendercontent);
    var t = $("#download-layout");
    t.find(".module").addClass("removeClean");
    t.find(".removeClean").each(function () {
        cleanHtml(this)
    });
    t.find(".removeClean").remove();
    t.find(".mce-content-body").removeClass("mce-content-body");
    t.find(".mce-edit-focus").removeClass("mce-edit-focus");
    t.find('.innerbg').closest('.devicewidth, .container').attr('hasbackground');
    t.find(".innerbg").remove();
    t.find(".buttonbg").remove();
    t.find(".addremove").remove();
    t.find(".uploader_wrap").remove();
    $('[id^="mce_"]').each(function(){
        $(this).removeAttr("id");
    });
    formatSrc = $.htmlClean($("#download-layout").html(), {
        format: true,
        allowComments:true,
        allowedAttributes: [
            ['id'],
            ["class"],
            ["width"],
            ["height"],
            ["style"],
            ["align"],
            ["cellspacing"],
            ["cellpadding"],
            ["leftmargin"],
            ["topmargin"],
            ["marginheight"],
            ["marginwidth"],
            ["yahoo"],
            ["bgcolor"],
            ["valign"],
            ["border"],
        ]
    });
    templateheader=$('textarea.templatehead').text();
    templatefooter=$('textarea.templatefooter').text();
    if ($('.editframe').length === 0) {
        var iframePreviewMobile;
        iframePreviewMobile = document.createElement("iframe");
        iframePreviewMobile.setAttribute("class", "editframe");
        iframePreviewMobile.setAttribute("frameborder", "0");
        iframePreviewMobile.setAttribute("width", "100%");
        iframePreviewMobile.setAttribute("style", "height=523px;");
        var iframeWrapper = document.getElementsByClassName('mobile_frame')[0];
        iframeWrapper.appendChild(iframePreviewMobile);
        var iframePreviewMobileContent = iframePreviewMobile.contentDocument;
        iframePreviewMobileContent.open();
        iframePreviewMobileContent.write('<!DOCTYPE HTML>');
        iframePreviewMobileContent.write("<html>");
        iframePreviewMobileContent.write("<head></head>");
        iframePreviewMobileContent.write("<body></body>");
        iframePreviewMobileContent.write("</html>");
        iframePreviewMobileContent.close();
    }
    var $iframe = $('.editframe');
    var $head = $iframe.contents().find("head");
    $head.empty();
    $head.append(templateheader);
    var $content = $("iframe.editframe").contents();
    var $body = $content.find("body");
    for(var attrName in bodyAttrs) {
        var value = bodyAttrs[attrName];
        $body.attr(attrName, value);
    }
    $body.empty();
    $body.append(formatSrc+templatefooter);
}
function exit_preview(){
    $('.mobile_frame').animate({'marginLeft':'-400px'});
    $('#main-content').animate({'marginLeft':'250px'});
    $('.save_clear a').fadeIn();
    $('#exit_preview').hide();
}







