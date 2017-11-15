$('.attention_div span').on('mouseenter', function () {
    $(this).find('h5').css('margin-left', '-30px')
    $(this).find('h5').animate({
        'margin-left': 0
    }, 200)

    var height = 180;
    if ($(window).width() + 17 <= 1024) {
        height = 130;
    }

    $(this).animate({
        height: height
    }, 200)
    $(this).find('.max_ewm').animate().stop();
    $(this).find('.max_ewm').animate({
        'height': '130px',
        opacity: 1
    }, 200)
});

$('.attention_div span').on('mouseleave', function () {
    $(this).animate().stop();
    $(this).css({
        height: 40
    })
    $(this).find('.max_ewm').animate({
        'height': '130px',
        opacity: 0
    }, 300)
    $(this).find('h5').css('line-height', '20px')

    $maxEwm = $(".max_ewm")

    $maxEwm.removeAttr('style')

    $(this).find('max_ewm')

})