$('#ljf .main').fullpage({
    paddingTop: '72px'
});
setInterval(function(){
    $.fn.fullpage.moveSlideRight();
}, 2000);