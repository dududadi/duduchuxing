$('#ljf .main').fullpage({
    paddingTop: '72px'
});
setInterval(function(){
    $.fn.fullpage.moveSlideRight();
}, 2000);
window.onload = function() {
    function rebuild() {
        var $boxs = $('.client .box');
        var winW = $(window).width();
        var winH = $(window).height();
        var decrease_w = winW*.8;
        $boxs.width(decrease_w);
        var $slideInner = $('#ljf #myCarousel .carousel-inner');
        var $slideInnerTop = 226;
        if (winW >970) {
            $slideInner.height('420px');
        } else {
            var newHeight = winH - $slideInnerTop - 20;
            var halfHeight = newHeight/2;
            $slideInner.css({height:newHeight});
            $slideInner.find('.item').children().css({height:halfHeight});
        }

    }
    rebuild();
    $(window).resize(function (e) {
        e.stopPropagation();
        rebuild();
    });
};