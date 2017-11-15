$('#ljf .main').fullpage({
    paddingTop: '72px'
});
setInterval(function(){
    $.fn.fullpage.moveSlideRight();
}, 2000);
function rebuild() {
    var $boxs = $('.client .box');
    var winW = $(window).width();
    $boxs.css({width:winW*.8});
}
rebuild();
$(window).resize(function (e) {
    e.stopPropagation();
    rebuild();
});