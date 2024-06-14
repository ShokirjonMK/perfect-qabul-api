$.urlParam = function (name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);

    if (results == null) {
        return null;
    } else {
        return decodeURI(results[1]) || 0;
    }
}

String.prototype.formatString = function () {
    "use strict";
    var str = this.toString();
    if (arguments.length) {
        var t = typeof arguments[0];
        var key;
        var args = ("string" === t || "number" === t) ?
            Array.prototype.slice.call(arguments)
            : arguments[0];

        for (key in args) {
            str = str.replace(new RegExp("\\{{" + key + "\\}}", "gi"), args[key]);
        }
    }

    return str;
};

function setCookie(name, value, days) {
    var expires = "";

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }

    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function validURL(str) {
    var pattern = new RegExp('^(https?:\\/\\/)?' +
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' +
        '((\\d{1,3}\\.){3}\\d{1,3}))' +
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' +
        '(\\?[;&a-z\\d%_.~+=-]*)?' +
        '(\\#[-a-z\\d_]*)?$', 'i');
    return !!pattern.test(str);
}

function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};

function goto(url) {
    if (url)
        window.location.href = url;
    else
        return false;
}

function isEmpty(str) {
    return !str.replace(/\s+/, '').length;
}

function ucfirst(str) {
    var text = str;
    var parts = text.split(' '),
        len = parts.length,
        i, words = [];
    for (i = 0; i < len; i++) {
        var part = parts[i];
        var first = part[0].toUpperCase();
        var rest = part.substring(1, part.length);
        var word = first + rest;
        words.push(word);
    }

    return words.join(' ');
};

function randomString(length) {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < length; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

function formatMoney(amount, decimalCount = 2, decimal = "", thousands = "") {
    try {
        decimalCount = Math.abs(decimalCount);
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

        if (typeof $_price_format_type !== 'undefined') {
            decimal = (isEmpty(decimal)) ? $_price_format_type[0] : decimal;
            thousands = (isEmpty(thousands)) ? $_price_format_type[1] : thousands;
        } else {
            decimal = (isEmpty(decimal)) ? '.' : decimal;
            thousands = (isEmpty(thousands)) ? ',' : thousands;
        }

        const negativeSign = amount < 0 ? "-" : "";

        let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
        let j = (i.length > 3) ? i.length % 3 : 0;

        return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
    } catch (e) {
        console.log(e)
    }
};

function notifySuccess(message, params) {
    var notifyTitle = translations.main.notification;
    var notifyTimeout = 3000;

    if (typeof params === 'object' && params !== null) {
        if (params.title != undefined && params.title != '') {
            notifyTitle = params.title;
        }

        if (!isNaN(params.timeout) && params.timeout > 0) {
            notifyTimeout = params.timeout;
        }
    } else if (!isNaN(params) && params > 0) {
        notifyTimeout = params;
    } else if (params != undefined && params != '') {
        notifyTitle = params;
    }

    $.notify({
        title: notifyTitle,
        body: message,
        icon: 'ri-checkbox-circle-line',
        id: 'notify-success',
        timeout: notifyTimeout
    });
}

function notifyError(message, params) {
    var notifyTitle = translations.main.error;
    var notifyTimeout = 3000;

    if (typeof params === 'object' && params !== null) {
        if (params.title != undefined && params.title != '') {
            notifyTitle = params.title;
        }

        if (!isNaN(params.timeout) && params.timeout > 0) {
            notifyTimeout = params.timeout;
        }
    } else if (!isNaN(params) && params > 0) {
        notifyTimeout = params;
    } else if (params != undefined && params != '') {
        notifyTitle = params;
    }

    if (message != '') {
        $.notify({
            title: notifyTitle,
            body: message,
            icon: 'ri-spam-2-line',
            id: 'notify-error',
            timeout: notifyTimeout,
        });
    } else {
        $.notify({
            title: notifyTitle,
            body: ajax_error_msg,
            icon: 'ri-spam-2-line',
            id: 'notify-error',
            timeout: notifyTimeout,
        });
    }
}

function notifyShow(title, message, icon, id, timeout) {
    var notifyTimeout = 3000;

    if (!isNaN(timeout) && timeout > 0) {
        var notifyTimeout = timeout;
    }

    $.notify({
        title: title,
        body: message,
        icon: icon,
        id: id,
        timeout: notifyTimeout
    });
}

function setFormError(formid, value_id) {
    if (formid && value_id) {
        $(formid + ' ' + value_id).each(function () {
            $(this).addClass('form-error');
        });
    }
}

function clearForm(formid) {
    if (formid != undefined) {
        $(formid).find('input[type="text"]').val('');
        $(formid).find('input[type="email"]').val('');
        $(formid).find('input[type="phone"]').val('');
        $(formid).find('input[type="password"]').val('');
        $(formid).find('select').val('');
        $(formid).find('textarea').val('');
    }
}

function ajaxData(data) {
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    var config = {
        '_ajax_token': randomString(8),
        '_csrf_token': csrfToken,
    };

    if (data != undefined && data != '') {
        var datas = Object.assign(data, config);
        return datas;
    } else {
        return config;
    }
}

function setRedirectTo(link) {
    if (link != undefined && link != '') {
        redirectTo = link;
    }
}

function checkUserID() {
    var id = parseInt(userid);

    if (!isNaN(id)) {
        return id;
    }
}

function screenSizeToggle() {
    var width = $(window).width();

    if (width >= 1200) {
        sdevice = 'xl';
    }

    if (width < 1200 && width >= 992) {
        sdevice = 'lg';
    }

    if (width < 992 && width >= 768) {
        sdevice = 'md';
    }

    if (width < 768 && width >= 576) {
        sdevice = 'sm';
    }

    if (width < 576) {
        sdevice = 'xs';
    }

    sdwidth = width;
}

$(window).on('resize', function () {
    screenSizeToggle();
});

$(document).ready(function () {
    screenSizeToggle();

    $('#scrollToTop').click(function () {
        $('html, body').animate({ scrollTop: 0 }, 'slow');
        return false;
    });

    $('[data-email]').each(function () {
        var email = $(this).attr('data-email');

        if (email != undefined && email != '') {
            $(this).attr('href', 'mailto:' + email);
        }
    });

    $('[data-phone]').each(function () {
        var phone = $(this).attr('data-phone');

        if (phone != undefined && phone != '') {
            $(this).attr('href', 'tel:' + phone);
        }
    });

    $('[data-parallax]').each(function () {
        var image_url = $(this).attr('data-parallax');

        if (image_url != undefined && image_url != '') {
            $(this).css('background-image', 'url(' + image_url + ')');
        }
    });

    $('[data-num]').click(function () {
        var type = $(this).data('num');
        var $input = $(this).parent().children('input');
        var number = parseInt($input.val());

        if (type == 'minus') {
            $input.val(number - 1);
        } else if (type == 'plus') {
            $input.val(number + 1);
        }
    });

    $('.custom-file-input').change(function () {
        var file = $(this)[0].files[0];

        if (file != undefined && file.name != undefined && file != '') {
            var $label = $(this).parent().children('.custom-file-label');
            $label.text(file.name);
        }
    });
});
