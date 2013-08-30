var icms = icms || {};

$(document).ready(function(){

    for(module in icms){
        if ( typeof(icms[module].onDocumentReady) == 'function' ) {
            icms[module].onDocumentReady();
        }
    }

    $('.widget_tabbed').each(function(){

       $('.tabs .tab a', $(this)).click(function(){
           var wid = $(this).data('id');
           var block = $(this).parent('li').parent('ul').parent('.tabs').parent('.widget_tabbed');
           $('.body', block).hide();
           $('.links-wrap', block).hide();
           $('#widget-'+wid, block).show();
           $('#widget-links-'+wid, block).show();
           $('.tabs a', block).removeClass('active');
           $(this).addClass('active');
           return false;
       });

    });

    $("<select>").appendTo("nav");

    $("nav .menu > li > a").each(function() {
        var el = $(this);
        $("<option>", {
            "value"   : el.attr("href"),
            "text"    : el.text()
        }).appendTo("nav select");
    });

    $("nav select").change(function() {
        window.location = $(this).find("option:selected").val();
    });

    if ($('.tabs-menu').length){

        $(".tabs-menu").each(function() {

            var tabs = $(this);

            var dropdown = $("<select>").appendTo(tabs);

            $("ul > li > a", tabs).each(function() {
                var el = $(this);
                $("<option>", {
                    "value"   : el.attr("href"),
                    "text"    : el.text()
                }).appendTo(dropdown);
            });

            $(dropdown).change(function() {
                window.location = $(this).find("option:selected").val();
            });

        });

    }

});

icms.forms = (function ($) {

    //====================================================================//

	this.toJSON = function(form) {
        var o = {};
        var a = form.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
	}

    //====================================================================//

    this.submit = function(){
        $('input:submit').trigger('click');
    }

    //====================================================================//

    this.submitAjax = function(form){

        var form_data = this.toJSON($(form));

        var url = $(form).attr('action');

        $.post(url, form_data, function(result){

            if (result.errors == false){
                if ("callback" in result){
                    window[result.callback](form_data);
                }
                return;
            }

            if (typeof(result.errors)=='object'){

                $('.field_error', form).removeClass('field_error');
                $('.error_text', form).remove();

                for(var field_id in result.errors){
                    var id = field_id.replace(':', '_');
                    $('#f_'+id, form).addClass('field_error');
                    $('#f_'+id, form).prepend('<div class="error_text">' + result.errors[field_id] + '</div>');
                }

                icms.modal.resize();

                return;

            }

        }, 'json');

        return false;

    }

	return this;

}).call(icms.forms || {},jQuery);

function toggleFilter(){
    var filter = $('.filter-panel');
    $('.filter-link', filter).slideToggle();
    $('.filter-container', filter).slideToggle();
}

function goBack(){
    window.history.go(-1);
}
