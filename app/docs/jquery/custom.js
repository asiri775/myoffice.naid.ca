/**
 * Created by me664 on 7/3/15.
 */
jQuery(document).ready(function($){

    $('.toctree-expand').click(function(){
       var li_parent=$(this).parent().parent('li');
        li_parent.siblings('li').removeClass('current').find('>ul').slideUp('fast');
        if(li_parent.length){
            if(li_parent.hasClass('current'))
            {
                li_parent.removeClass('current').find('>ul').slideUp('fast');
            }else
            {
                li_parent.addClass('current').find('>ul').slideDown('fast');
            }
        }
    });

    $('.menu-item-has-children>a').click(function(){
        var li_parent=$(this).parent('li');
        li_parent.siblings('li').removeClass('current').find('>ul').slideUp('fast');
        if(li_parent.length){
            if(li_parent.hasClass('current'))
            {
                li_parent.removeClass('current').find('>ul').slideUp('fast');
            }else
            {
                li_parent.addClass('current').find('>ul').slideDown('fast');
            }
        }

    });

    $('.section.document-section-item:first-child').fadeIn('fast');
    $('.current-section').html($('.menu>li:first-child>a').text());

    $('li a.menu-item').click(function(e,no_need_push){
        var li_parent=$(this).parent('li');
        li_parent.siblings('li').removeClass('active');
        li_parent.addClass('active');

        var href=$(this).attr('href');
        if($(href).length && $(this).parent().hasClass('current')==false){

            $('.document-section-item').hide();
            $(href).parents('.document-section-item').show();
            $(href).fadeIn('fast').find('.document-section-item:first-child').fadeIn('fast');

            $('.current-section').html($(this).html());
        }
        if(typeof no_need_push=='undefined' && !no_need_push)
        window.history.pushState('Object', 'Title', href);

        return false;
    });

    $('.sub-menu li').click(function(){
        $(this).siblings('li').removeClass('current');
    });

    // check the current id from url
    var page_id=window.location.hash;
    //
    //if(page_id && $('a[href='+page_id+']').length)
    //{
    //    $('a[href='+page_id+']').trigger('click');
    //    $('a[href='+page_id+']').parent().parents('li.menu-item:not(.current)').find('>a').trigger('click');
    //}

    if($(page_id).closest('.document-section-item').length)
    {
        var section_id=$(page_id).closest('.document-section-item').attr('id');
        console.log(section_id);
        if($('a[href=#'+section_id+']').length){
            $('a[href=#'+section_id+']').trigger('click',true);
            $('a[href=#'+section_id+']').parent().parents('li.menu-item:not(.current)').find('>a').trigger('click',true);
        }
		
		    
		
    }
	if( page_id ){
			console.log(page_id);
			setTimeout(function(){
				$([document.documentElement, document.body]).animate({
					scrollTop: $(page_id).offset().top - 30
				}, 1000);
			},500);
			
	}

    $(".wy-nav-top .fa").click(function () {
        $('.wy-nav-side').toggleClass("active")
    });
});


