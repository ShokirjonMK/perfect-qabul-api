$(document).ready(function () {
    $(document).on('submit', '.translation-form', function () {
        translation_changed = false;
    });

    $(document).on('change', '.translation-input-text', function () {
        translation_changed = true;

        var value = $(this).val();
        var trskey = $(this).attr('data-translation-touch');

        $(this).parent().children('span').html(value);
        $('[data-translation-id="' + trskey + '"]').val(value);
    });

    $(document).on('click', '[data-scan-translations]', function () {
        var $this = $(this);
        var path = $this.attr('data-scan-translations');
        var redirect_to = $this.attr('data-redirect-to');

        if (path != undefined && path != '') {
            $.ajax({
                url: translation_ajax_url,
                type: "POST",
                data: {
                    action: 'scan-translations',
                    path: path,
                },
                dataType: "json",
                beforeSend: function () {
                    $this.addClass('preloader-btn--active');
                },
                success: function (data) {
                    if (data.success) {
                        toastr.success(data.message, trs.messages.success, { timeOut: 10000 });
                    } else {
                        toastr.error(data.message, trs.messages.error, { timeOut: 10000 });
                    }

                    if (data.count != undefined && data.count != '') {
                        $('[translation-count="' + path + '"]').text(data.count);
                    } else {
                        $('[translation-count="' + path + '"]').text('0');
                    }

                    if (data.date != undefined && data.date != '') {
                        $('[translation-date="' + path + '"]').text(data.date);
                    } else {
                        $('[translation-date="' + path + '"]').text('-');
                    }

                    if (redirect_to != undefined && redirect_to != '') {
                        setTimeout(function () {
                            window.location.href = redirect_to;
                        }, 1000);
                    }

                    $this.removeClass('preloader-btn--active');
                },
                error: function () {
                    alert(ajax_error_msg);
                    $this.removeClass('preloader-btn--active');
                }
            });
        } else {
            alert(trs.translations.path_error);
        }
    });
});