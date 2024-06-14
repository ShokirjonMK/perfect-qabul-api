var menu_page_ajax_item_type = '';
var menu_page_ajax_item_lang = 'en';

$(document).ready(function () {
    menuPageItemsInit();
    menuPageSortableInit();
});

function menuPageItemsInit() {
    var items_block = $('.menu-sortable-block');
    menuPageMenuItemsLoad(items_block, 0);

    $('.menu-items-box-add-b').click(function () {
        var parent = $(this).parent();
        var dropdown = parent.find('.menu-items-box-add-dropdown');

        if (dropdown != undefined && dropdown.length > 0) {
            if (dropdown.css('display') != 'none') {
                dropdown.removeClass('animated fadeInUp2').fadeOut();
            } else {
                dropdown.addClass('animated fadeInUp2').show();
            }
        }
    });

    $(document).mouseup(function (e) {
        var container = $(".menu-items-box-add");
        var dropdown = $(".menu-items-box-add-dropdown");

        if (!container.is(e.target) && container.has(e.target).length === 0) {
            dropdown.hide();
        }
    });

    $('[data-add-menu]').click(function () {
        var type = $(this).attr('data-add-menu');

        if (type != undefined && type != '') {
            var link_modal_id = '#addMenuItemLink';
            var box_modal_id = '#addMenuItemBox';
            var mod_modal_id = '#';

            if (type == 'link') {
                $(link_modal_id).modal('show');
            } else {
                menu_page_ajax_item_type = type;
                menu_page_ajax_item_lang = $('.c-translation-select').val();

                $(box_modal_id).modal('show');
                menuPageSearchItems();
            }
        }
    });

    $(document).on('click', '.menu-page-item-delete', function () {
        var parent = $(this).closest('.menu-page-item-box');

        if (confirm(trs.messages.delete_menu_item_qsn)) {
            parent.remove();
            menuPageMenuItemsLoad(items_block, 0);

            var menu_items = $('.menu-sortable-block .menu-page-item-box');

            if (menu_items != undefined && menu_items.length > 0) {
                $('.menu-items-notfound').hide().removeClass('d-none');
            } else {
                $('.menu-items-notfound').show().removeClass('d-none');
            }
        }
    });

    $(document).on('click', '.menu-page-item-edit', function () {
        var parent = $(this).closest('.menu-page-item-box-in');
        var info_box = parent.find('.menu-page-item-box-info');

        if (info_box != undefined && info_box.length > 0) {
            if (info_box.css('display') != 'none') {
                info_box.slideUp(200);
            } else {
                info_box.slideDown(200);
            }
        }
    });

    $(document).on('change keyup paste', '.menu-title-input', function () {
        var parent = $(this).closest('.menu-page-item-box-in');
        var val = $(this).val();
        var value = val.replace(/<[^>]*>?/gm, '');

        if (value) {
            parent.find('.menu-page-item-box-title span').text(value);
        }
    });

    $(document).on('submit', '.form-add-menu-item', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        var block = $('.menu-items-c');
        var notfound = $('.menu-items-notfound');

        $.ajax({
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function name(data) {
                if (data.success) {
                    notfound.hide().removeClass('d-none');
                    block.append(data.view);
                    $('.form-add-menu-item .form-input').val('');
                    toastr.success(data.message, 'Success', { timeOut: 10000 });
                } else {
                    toastr.error(data.message, 'Error', { timeOut: 10000 });
                }

                menuPageSortableInit();
                menuPageMenuItemsLoad(items_block, 0);
            }
        });
    });

    $('#addMenuItemBoxSubmit').click(function () {
        var selected = [];
        var block = $('.menu-items-c');
        var notfound = $('.menu-items-notfound');

        $('#addMenuItemBox .menu-ajax-checkbox:checked').each(function () {
            selected.push($(this).val());
        });

        $.ajax({
            type: 'POST',
            data: {
                ajax_action: 'add-menu-item',
                action_type: menu_page_ajax_item_type,
                selected_items: true,
                selected_array: selected,
            },
            dataType: 'json',
            success: function name(data) {
                if (data.success) {
                    notfound.hide().removeClass('d-none');
                    block.append(data.view);
                    $('.form-add-menu-item .form-input').val('');
                    toastr.success(data.message, 'Success', { timeOut: 10000 });

                    $('#addMenuItemBox .menu-ajax-checkbox:checked').each(function () {
                        $(this).prop('checked', false);
                    });
                } else {
                    toastr.error(data.message, 'Error', { timeOut: 10000 });
                }

                menuPageSortableInit();
                menuPageMenuItemsLoad(items_block, 0);
            }
        });
    });

    $('#addMenuItemBoxSearch').keypress(function (e) {
        if (e.which == 13) {
            var value = $(this).val();
            var resultsBlock = $('#addMenuItemBox .addMenuItemBoxResultsIn');
            var preloaderBlock = $('#addMenuItemBox .addMenuItemBoxPreloader');

            $.ajax({
                type: 'POST',
                data: {
                    ajax_action: 'add-menu-item',
                    action_type: menu_page_ajax_item_type,
                    search_items: true,
                    search: value,
                },
                dataType: 'json',
                beforeSend: function () {
                    preloaderBlock.show();
                    resultsBlock.empty();
                },
                success: function (data) {
                    preloaderBlock.hide();

                    if (data.success) {
                        resultsBlock.html(data.html);
                    } else {
                        resultsBlock.html('<b class="error-text-block">' + data.message + '</b>');
                    }
                }
            });
        }
    });

    $('#addMenuItemBoxSearch').on('keyup paste change', function () {
        var value = $(this).val();
        var $close_icon = $(this).parent().find('#addMenuItemBoxSearchClose');

        if (value != '') {
            $close_icon.stop().show();
        } else {
            $close_icon.stop().hide();
        }
    });

    $('#addMenuItemBoxSearchClose').on('click', function () {
        var input = $(this).parent().find('#addMenuItemBoxSearch');

        input.val('');
        $(this).hide();
        menuPageSearchItems();
    });

    $('#addMenuItemBox').on('hidden.bs.modal', function (e) {
        $('#addMenuItemBoxSearch').val('');
        $('#addMenuItemBoxSearchClose').hide();
    });
}

function menuPageSearchItems() {
    var resultsBlock = $('#addMenuItemBox .addMenuItemBoxResultsIn');
    var preloaderBlock = $('#addMenuItemBox .addMenuItemBoxPreloader');

    $.ajax({
        type: 'POST',
        data: {
            ajax_action: 'add-menu-item',
            action_type: menu_page_ajax_item_type,
            lang: menu_page_ajax_item_lang
        },
        dataType: 'json',
        beforeSend: function () {
            preloaderBlock.show();
            resultsBlock.empty();
        },
        success: function (data) {
            preloaderBlock.hide();

            if (data.success) {
                resultsBlock.html(data.html);
            } else {
                resultsBlock.html('<b class="error-text-block">' + data.message + '</b>');
            }
        }
    });
}

function menuPageSortableInit() {
    var items_block = $('.menu-sortable-block');

    $('.menu-sortable').sortable({
        handle: '.menu-page-item-box-title',
        draggable: '.menu-page-item-box',
        ghostClass: "sortable-ghost",
        chosenClass: "sortable-chosen",
        dragClass: "sortable-drag",
        animation: 150,
        group: 'nested',
        swapThreshold: 2,
        invertSwap: true,
        onStart: function (event) {
            items_block.addClass('menu-sortable--choosed');
        },
        onUnchoose: function (event) {
            items_block.removeClass('menu-sortable--choosed');
        },
        onEnd: function (event) {
            menuPageMenuItemsLoad(items_block, 0);
        },
    });
}

function menuPageMenuItemsLoad(element, i) {
    if (element != undefined && element.length > 0) {
        var childs = element.children('.menu-page-item-box');

        if (childs != undefined && childs.length > 0) {
            childs.each(function name() {
                i++;
                var id = parseInt($(this).attr('data-id'));
                var inputs = $(this).find('[data-name]');

                inputs.each(function () {
                    var input_attr = $(this).attr('data-name');

                    if (input_attr != '') {
                        var input_attr_val = 'menu_items[' + i + '][' + input_attr + ']';
                        $(this).attr('name', input_attr_val);
                    }

                    if (input_attr == 'id' && !isNaN(id)) {
                        $(this).attr('value', id);
                    }
                });

                var childbox = $(this).children('.menu-page-item-box-list');
                var child = childbox.children('.menu-page-item-box');

                if (child != undefined && child.length > 0) {
                    var attr = 'menu_items[' + i + '][childs]';
                    menuPageMenuItemsChildLoad(childbox, attr);
                }
            });
        }
    }
}

function menuPageMenuItemsChildLoad(element, attr) {
    var i = 0;

    if (element != undefined && element.length > 0) {
        var childs = element.children('.menu-page-item-box');

        if (childs != undefined && childs.length > 0) {
            childs.each(function name() {
                i++;
                var id = parseInt($(this).attr('data-id'));
                var inputs = $(this).find('[data-name]');

                inputs.each(function () {
                    var input_attr = $(this).attr('data-name');

                    if (input_attr != '') {
                        var input_attr_val = attr + '[' + i + '][' + input_attr + ']';
                        $(this).attr('name', input_attr_val);
                    }

                    if (input_attr == 'id' && !isNaN(id)) {
                        $(this).attr('value', id);
                    }
                });

                var childbox = $(this).children('.menu-page-item-box-list');
                var child = childbox.children('.menu-page-item-box');

                if (child != undefined && child.length > 0) {
                    var attr2 = attr + '[' + i + '][childs]';
                    menuPageMenuItemsChildLoad(childbox, attr2);
                }
            });
        }
    }
}