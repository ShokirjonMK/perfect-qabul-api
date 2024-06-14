$(document).ready(function () {
    // Select linked
    $('.select-linked').select2({
        width: 'auto',
        dropdownAutoWidth: true,
    });

    // Select linked on change
    $(document).on('change', '.select-linked', function () {
        var link = $(this).val();
        var param = $(this).attr('data-param');

        if (link == '-') {
            if (param != undefined && param != '') {
                gplinkRemoveParam(param);
            }
            location.reload();
        } else if (link) {
            location.href = link;
        }
    });

    // Slug eraser
    $(document).on('click', '.input-on-slug-eraser', function () {
        var parent = $(this).parent();
        var input = parent.children('input.form-control');

        if (input != undefined && input.length > 0) {
            input.val('');
        }
    });

    // Check span in title
    $('[ep-bind="title"]').each(function () {
        var span = $(this).children('span');

        if (span != undefined && span.length > 0) {
            return true;
        } else {
            $(this).append('<span></span>');
        }
    });

    // Check span in title
    $(document).on('keyup change', '[ep-bind-action="title"]', function () {
        var value = $(this).val();
        var title = value.replace(/(<([^>]+)>)/ig, "");

        if (title != '') {
            $('[ep-bind="title"] > span').text(title);
            $('[data-bind-translation]').val(title);
        } else {
            $('[ep-bind="title"] > span').empty();
            $('[data-bind-translation]').val('');
        }
    });

    // Translation select
    var $translation_select = $('.c-translation-select');

    if ($translation_select != undefined && $translation_select.length > 0) {
        $(document).on('change', '.c-translation-select', function () {
            translationInputBind();
        });

        // Translation input bind
        translationInputBind();
    }
});

// Translation input bind
function translationInputBind() {
    var lang_code = $('.c-translation-select').val();
    var input = $('[ep-bind-action="title"]').val();
    var input_value = '';

    if (input != undefined && input != '') {
        input_value = input.replace(/(<([^>]+)>)/ig, "");
    }

    if (lang_code != undefined && lang_code != '') {
        var attr = {
            'data-bind-translation': lang_code,
            'readonly': 'readonly',
        };

        $('[data-translation-code]').removeAttr('data-bind-translation').removeAttr('readonly');
        $('[data-translation-code="' + lang_code + '"]').attr(attr).val(input_value);
    }
}