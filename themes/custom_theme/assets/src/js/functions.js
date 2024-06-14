window.preloader = function(toggle) {
    if (toggle != undefined && toggle == 'show') {
        $('#preloader').fadeIn();
    } else {
        $('#preloader').fadeOut(300);
    }
}

window.body_overflow = function(type) {
    if (type != undefined && type == 'hidden') {
        $('body').addClass('overflow-hidden');
    } else {
        $('body').removeClass('overflow-hidden');
    }
}