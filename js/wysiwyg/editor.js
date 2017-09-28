function removeElm() {
    $(".emailrender").delegate(".remove", "click", function (e) {
        e.preventDefault();
        $(this).closest('table').parents('table').parents('.module ').slideUp('200', function(){  $(this).remove(); } );
        $(this).closest('.remove').css("opacity","0");
        $(this).parents('.view').find('table:first').find('table:first').removeClass('selecthoverred');
        $(this).parents('.view').find('.move_controls').show();
    });
}
function clearDemo() {
    $(".emailrender").empty()
}
function removeMenuClasses() {
    $("#menu-layoutit li button").removeClass("active")
}
function cleanHtml(e) {
    $(e).parent().append($(e).children().html())
}
function downloadLayoutSrc() {
    var e = "";
    var emailrendercontent=$(".emailrender").html();
    $("#download-layout").html(emailrendercontent);
    var t = $("#download-layout");
    var contentattr=$('[st-content]').attr( "st-content" );
    t.find(".preview, .configuration, .move_controls, .addremove, .colorSelectorinner").remove();
    t.find(".module").addClass("removeClean");
    t.find(".box-element").addClass("removeClean");
    t.find(".module .module .module .module .module .removeClean").each(function () {
        cleanHtml(this)
    });
    t.find(".module .module .module .module .removeClean").each(function () {
        cleanHtml(this)
    });
    t.find(".module .module .module .removeClean").each(function () {
        cleanHtml(this)
    });
    t.find(".module .module .removeClean").each(function () {
        cleanHtml(this)
    });
    t.find(".module .removeClean").each(function () {
        cleanHtml(this)
    });
    t.find(".removeClean").each(function () {
        cleanHtml(this)
    });
    t.find(".removeClean").remove();
    t.find(".mce-content-body").removeClass("mce-content-body");
    formatSrc = $.htmlClean($("#download-layout").html(), {
        format: true,
        allowComments:true,
        allowedAttributes: [
            ["class"],
            ["width"],
            ["height"],
            ["style"],
            ["align"],
            ["cellspacing"],
            ["cellpadding"],
            ["bgcolor"],
            ["valign"],
            ["border"],
            ["st-content"],
            ["st-sortable"],
            ["st-image"],
            ["st-title"],
            ["st-webversion"],
            ["st-unsubscribe"],
            ["mc:edit"],
            ['mc:label'],
            ["mc:repeatable"],
        ]
    });
    var $body = $(formatSrc).find("body");
    for(var attrName in bodyAttrs) {
        var value = bodyAttrs[attrName];
        $body.attr(attrName, value);
    }
    $("#download-layout").html(formatSrc);
    templateheader=$('textarea.templatehead').text();
    templatefooter=$('textarea.templatefooter').text();
    $("#downloadModal textarea").empty();
    $("#downloadModal textarea").val(templateheader+formatSrc+templatefooter);
}
var currentDocument = null;
var timerSave = 2e3;
$(document).ready(function () {
    $("[data-target=#downloadModal]").click(function (e) {
        e.preventDefault();
        downloadLayoutSrc()
    });
    $("#edit").click(function () {
        $("body").removeClass("devpreview sourcepreview");
        $("body").addClass("edit");
        removeMenuClasses();
        $(this).addClass("active");
        return false
    });
    $("a#clear").click(function (e) {
        e.preventDefault();
        clearDemo();
    });
    $("#devpreview").click(function () {
        $("body").removeClass("edit sourcepreview");
        $("body").addClass("devpreview");
        removeMenuClasses();
        $(this).addClass("active");
        return false
    });
    $("#sourcepreview").click(function () {
        $("body").removeClass("edit");
        $("body").addClass("devpreview sourcepreview");
        removeMenuClasses();
        $(this).addClass("active");
        return false
    });
    $(".nav-header").click(function () {
        $(".sidebar-nav .boxes, .sidebar-nav .rows").hide();
        $(this).next().slideDown()
    });
    removeElm();
});
