function saveLayoutSrc(id) {
    var e = "";
    $(".emailrender").find('.innerbg').closest('.devicewidth, .container').attr('hasbackground', 'true');
    $('.emailrender').css({'height': 'auto'});
    var emailrendercontent = $(".emailrender").html();
    var t = $("#download-layout");
    t.html(emailrendercontent);
    t.find('.innerbg').closest('.devicewidth, .container').each(function(i, el){
        var $el = $(el);
        if(!$el.attr('bgcolor')) {
            var col = $el.closest('table').attr('bgcolor');
            $el.attr('bgcolor', col);
            $el.attr('hasbackground', true);
        }
    });
    t.find('.ui-sortable-helper').remove();
    t.find('[st-content], [st-title]').removeAttr('id');
    t.find('.innerbg').closest('.devicewidth, .container').attr('hasbackground', 'true');
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
    t.find(".innerbg").remove();
    t.find(".buttonbg").remove();
    t.find(".uploader_wrap").remove();
    t.find(".mce-panel").remove();
    t.find('[st-unsubscribe]').each(function() {
        $(this).attr('st-unsubscribe', 'st-unsubscribe');
    });
    t.find('[st-webversion]').each(function() {
        $(this).attr('st-webversion', 'st-webversion');
    });
    var formatSrc = $.htmlClean(t.html(), {
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
            ["bgcolor"],
            ["valign"],
            ["border"],
            ["hasbackground"],
            ["st-content"],
            ["st-title"],
            ["st-sortable"],
            ["st-bgcolor"],
            ["st-image"],
            ["st-unsubscribe"],
            ["st-webversion"],
            ["st-button"]
        ]
    });
    t.html(formatSrc);
    var $savehtml = "<html>\n" + "<head>" + $('textarea.templatehead').text() + "</head>" + "<body>" + formatSrc + "</body>\n</html>";
    var list_id=$('#list_id').val();
    var token=$('#token').val();
    var r = $.Deferred();
    var data = { html: $savehtml,list_id: list_id,token:token };
    var a = $.ajax
    (
        {
            url:"ajax/save.php",
            type:'POST',
            data:data
        }
    );
    a.done(function(){
        $('#notifications').addClass('saving');
        setTimeout(function(){
            $('#notifications.saving').find('.note').fadeOut(function(){
                $('#notifications.saving').find('.note').remove();
            });
        }, 4000);
    });
    return r;
}
$(document).ready(function () {
    $('.savebtn').click(function(event){
        event.preventDefault();
        $('#notifications').empty();
        $('#notifications').append('<div class="note"><div class="note_green"></div><div class="note_msg">Saving</div></div>');
        saveLayoutSrc($(this).attr("data-id"));
    });
    setInterval(function(){saveLayoutSrc($('.savebtn').data('id'))}, 10000);
});






