var icms = icms || {};

icms.photos = (function ($) {

    this.lock = false;
    this.init = false;

    //====================================================================//

    this.onDocumentReady = function(){

        if (this.init == false) { return; }

        $('#album-nav').width(slider_w+64);
        $('#photos-slider').width(slider_w);
        $('#photos-slider ul').width(li_w * li_count);

        if (slide_left < min_left) { slide_left = 0; } else
        if (slide_left > max_left) { slide_left = max_left; } else {
            slide_left = slide_left - (li_w * left_li_offset);
        }

        $('#photos-slider ul').css('margin-left', '-'+slide_left+'px');

        icms.photos.toggleArrows();

        $('#album-nav .arr-prev a').click(function(){

            if (icms.photos.lock){ return; }

            if (parseInt($('#photos-slider ul').css('margin-left'), 10) < 0){

                icms.photos.lock = true;

                $('#photos-slider ul').animate({marginLeft: '+=' + li_w}, 200, function(){
                    icms.photos.toggleArrows();
                    icms.photos.lock = false;
                });

            }

        });

        $('#album-nav .arr-next a').click(function(){

            if (icms.photos.lock){ return; }

            if ((-1*max_left) < parseInt($('#photos-slider ul').css('margin-left'), 10)){
                icms.photos.lock = true;
                $('#photos-slider ul').animate({marginLeft: '-=' + li_w}, 200, function(){
                    icms.photos.toggleArrows();
                    icms.photos.lock = false;
                });
            }

        });


    }

    //====================================================================//

    this.toggleArrows = function (){

        if (li_count <= li_in_frame){
            $('#album-nav .arr-prev a').hide();
            $('#album-nav .arr-next a').hide();
            return;
        }

        if (parseInt($('#photos-slider ul').css('margin-left'), 10) == 0){
            $('#album-nav .arr-prev a').hide();
        } else {
            $('#album-nav .arr-prev a').show();
        }

        if ((-1*max_left) == parseInt($('#photos-slider ul').css('margin-left'), 10)){
            $('#album-nav .arr-next a').hide();
        } else {
            $('#album-nav .arr-next a').show();
        }

    }

    //====================================================================//

    this.createUploader = function(upload_url){

        var uploader = new qq.FileUploader({
            element: document.getElementById('album-photos-uploader'),
            action: upload_url,
            debug: false,

            onComplete: function(id, file_name, result){

                if(!result.success) { return; }

                var widget = $('#album-photos-widget');
                var preview_block = $('.preview_template', widget).clone().removeClass('preview_template').addClass('preview').attr('rel', result.id).show();

                $('img', preview_block).attr('src', result.url);
                $('.title input', preview_block).attr('name', 'photos['+result.id+']');
                $('a', preview_block).click(function() { icms.photos.removeOne(result.id); });

                $('.previews_list', widget).append(preview_block);

            }

        });

    }

    //====================================================================//

    this.remove = function(id){

        var widget = $('#album-photos-widget');

        var url = widget.data('delete-url') + '/' + id;

        $.post(url, {}, function(result){

            if (!result.success) { return; }

            $('.preview[rel='+id+']', widget).fadeOut(400, function() { $(this).remove(); });

        }, 'json');

        return false;

    }

    //====================================================================//

	return this;

}).call(icms.photos || {},jQuery);
