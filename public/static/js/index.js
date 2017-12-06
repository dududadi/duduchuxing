$('#ljf .main').fullpage({});
setInterval(function(){
    $.fn.fullpage.moveSlideRight();
}, 2000);

function rebuild() {
    var $boxs = $('.client .box');
    var winW = $(window).width();
    var winH = $(window).height();
    var decrease_w = winW*.8;
    $boxs.width(decrease_w);
    var $slideInner = $('#ljf #myCarousel .carousel-inner');
    var $slideInnerTop = 226;
    if (winW > 970) {
        if (winW > 1366) {
            $slideInner.height('460px');
        } else {
            if (screen.width > 1366) {
                $slideInner.height('420px');
            } else {
                $slideInner.height('400px');
            }
        }
        $slideInner.find('.item').children().css({height:'100%'});
    } else {
        var newHeight = winH - $slideInnerTop - 20;
        var halfHeight = newHeight/2;
        $slideInner.css({height:newHeight});
        $slideInner.find('.item').children().css({height:halfHeight});
    }

}

window.onload = function () {
    rebuild();
    $(window).resize(function (e) {
        e.stopPropagation();
        rebuild();
    });

    if (screen.width >= 1920) {
        var $footerChild = $('.row').eq(3);
        var remain = Math.abs(($('body').height() - parseInt($footerChild.position().top) - 147)/4);
        $('#download').children().css({'margin': remain+'px 0px'});
    } else {
        var $footerChild = $('.row').eq(3);
        var remain = Math.abs(($('body').height() - parseInt($footerChild.position().top)));
        $footerChild.children().last().css({'marginBottom': remain});
        if (screen.width <= 1366) $footerChild.hide();
    }
};