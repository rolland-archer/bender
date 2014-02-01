/**
* @param string title
* @param string message
* @param function confirm
* @param function discard
* @param string(confirm/alert) type
}
* 
*/

(function( $ ){

    $.confirm = function(params) {
        var config = {
            title: '',
            message: '',
            close: function(){},
            confirm: function(){},
            discard: function(){},
            type: 'confirm'
        };
        
        $.extend(
            config, 
            params);
        config.message = config.message.replace(/(\n)/ig,"<br>");
        $('html').addClass('fancybox-lock');
        var content = '<div class="fancybox-overlay fancybox-overlay-fixed"><div class="confirm-wrapper"><div class="confirm-outer"><div class="confirm-inner">';
            content += '<div class="confirm-title">'+ config.title +'</div>';
            content += '<div class="confirm-message">'+ config.message +'</div>';
            content += '<div class="confirm-buttons"><a href="#" class="light-gray-btn stretch confirm-js"><span><span class="btn-caption">Да</span><span class="spring"></span></span></a>'+(config.type == 'confirm' ? '<a href="#" class="red-btn stretch discard-js"><span><span class="btn-caption">Нет</span><span class="spring"></span></span></a>' : '')+'</div>';
            content += '</div></div></div></div>';
        
        content = $(content);
        content.appendTo('body').show();
        
        // закрытие конфирма
        content.find('.confirm-buttons a').click(function(){
            content.remove();
            $('html').removeClass('fancybox-lock');
            if (typeof config.close == 'function')
                config.close();
        });
        
        //выравниваем по центру
        var wrap = content.find('.confirm-wrapper');
        wrap.css({marginLeft: -Math.round(wrap.outerWidth()/2)});
        
        if (wrap.outerHeight() < $(window).height())
            wrap.css({marginTop: -Math.round(wrap.outerHeight()/2)});
        else
            wrap.css({marginTop: 0, top: 0});
        
        if (typeof config.confirm == 'function')
            content.find('.confirm-buttons a.confirm-js').click(config.confirm);
        if (typeof config.discard == 'function')
            content.find('.confirm-buttons a.discard-js').click(config.discard);
    };
    
    $.alert = function(params) {
        var config = {
            title: 'Внимание',
            message: '',
            close: function(){}
        };
        if(typeof params == 'string'){
            params = {message:params};    
        }
        $.extend(
            config, 
            params); 
        config.type = 'alert';
        $.confirm(config);   
    }
    

})( jQuery );