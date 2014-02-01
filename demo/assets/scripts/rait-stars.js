(function( $ ){
    'use strict';
    var methods = {
        init : function() {
            var obj = $(this).empty(),
                val = obj.data('val') != 'undefined' ? obj.data('val') : 0,
                fractional = val%1,
                integral  = val - fractional;    
            for (var i = 1; i <= 5; i++)
            {
                var mark = $('<span></span>');
                if (i <= integral)
                    mark.append('<i></i>');
                else if (fractional && integral==(i-1))
                    mark.append('<i style="width: '+parseInt(fractional*100)+'%;"></i>');
                obj.append(mark);
            }
        }
    };

    $.fn.raitStars = function() {
    this.each(function(){
    return methods.init.apply(this);
    });
    };

})( jQuery );