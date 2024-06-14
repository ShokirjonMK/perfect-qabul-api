var storage_browser_called = false;
var storage_browser_url = site_url + 'storage/';
var storage_browser_path = '/';
var storage_browser_view_type = 'list';
var storage_browser_popup_ = 'single';
var storage_browser_frame_id;
var storage_browser_frame_item;
var storage_browser_frame_action;
var storage_browser_frame_select;
var storage_browser_event;
var storage_browser_event_data;
var storage_browser_event_id = '#storage_browser_event';

$(document).ready(function () {
    // Storage browser event input
    if ($(storage_browser_event_id) != undefined && $(storage_browser_event_id).length > 0) {
        storage_browser_event = $(storage_browser_event_id);
    } else {
        $('body').append('<input type="hidden" id="storage_browser_event">');
        storage_browser_event = $(storage_browser_event_id);
    }

    // Run frame
    storageBrowserFrame();
});

function storageBrowserInit() {
    storage_browser_called = true;

    var path_name = mbUrlParameter('path');
    var view_type = mbUrlParameter('view');

    if (path_name != undefined && path_name != '') {
        path_name = path_name.replace(/\/$/, "");
        storage_browser_path = path_name;
    }

    if (view_type != undefined && view_type == 'grid') {
        storage_browser_view_type = 'grid';
    }

    // Select box
    storageBrowserSelect();

    // Toggle areas
    $(document).on('click', '[storage-browser-toggle]', function () {
        var action = $(this).attr('storage-browser-toggle');

        if (action != undefined && action != '') {
            var block_folder = $('.storage-browser-add-folder');
            var block_upload = $('.storage-browser-upload');

            if (action == 'folder') {
                block_folder.toggleClass('animated fadeIn');
            } else if (action == 'upload') {
                block_upload.toggleClass('animated fadeIn');
            }
        }
    });

    // Popup close
    $(document).on('click', '[storage-browser-popup-close]', function () {
        storageBrowserPopup('hide');
    });

    // File info show
    $(document).on('click', '[storage-browser-info]', function () {
        var file_name = $(this).attr('storage-browser-info');

        if (file_name != undefined && file_name != '') {
            var item = $('[storage-browser-info-block="' + file_name + '"]');
            storageBrowserPopup('show', item);
        }
    });

    // Folder info show
    $(document).on('click', '[storage-browser-path-info]', function (e) {
        e.preventDefault();
        var file_name = $(this).attr('storage-browser-path-info');

        if (file_name != undefined && file_name != '') {
            var item = $('[storage-browser-info-block="' + file_name + '"]');
            storageBrowserPopup('show', item);
        }
    });

    // Submit file edit form
    $(document).on('submit', '.storage-browser-action-form', function (e) {
        e.preventDefault();

        var $this = $(this);
        var form_data = $this.serialize();
        var action_type = $this.find('input[name="action_type"]');
        var $button = $this.find('.storage-browser-action-btn');

        $.ajax({
            url: storage_browser_url + 'actions/?path=' + storage_browser_path,
            type: "POST",
            data: form_data,
            dataType: "json",
            beforeSend: function () {
                $button.addClass('with-preloader-icon');
            },
            success: function (data) {
                $button.removeClass('with-preloader-icon');

                if (data.message != undefined && data.message != '') {
                    var message = data.message;

                    if (data.success) {
                        storageBrowserPopup('hide');
                        storageBrowserAjax();
                        toastr.success(message, 'Success', { timeOut: 10000 });

                        if (action_type != undefined && action_type.val() == 'create_folder') {
                            $('input[name="folder_name"]').val('');
                        }
                    } else {
                        toastr.error(message, 'Error', { timeOut: 10000 });
                    }
                }
            },
            error: function () {
                alert(ajax_error_msg);
            }
        });
    });

    // Folder open
    $(document).on('click', '[storage-browser-open-dir]', function (e) {
        var folder_icon = $(".storage-browser-table-item-info");

        if (!folder_icon.is(e.target) && folder_icon.has(e.target).length === 0) {
            var path_name = $(this).attr('storage-browser-open-dir');

            if (path_name != undefined && path_name != '') {
                storageBrowserAjax(path_name);
            } else {
                alert(trs.messages.stb_invalid_path);
            }
        }
    });

    // Quick actions
    $(document).on('click', '[storage-browser-quick-action]', function () {
        var items = [];
        var quick_action = $(this).attr('storage-browser-quick-action');

        if (quick_action != undefined && quick_action != '') {
            var objects = $('[storage-browser-selected]');

            if (objects != undefined && objects.length > 0) {
                objects.each(function () {
                    var value = $(this).attr('storage-browser-selected');

                    if (value != undefined && value != '') {
                        items.push(value);
                    }
                });
            }
        }

        if (items.length > 0) {
            Swal.fire({
                title: trs.messages.delete,
                text: trs.messages.ta_delete_items_qsn,
                icon: 'warning',
                showCancelButton: !0,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#ff3d60",
                confirmButtonText: trs.messages.yes
            }).then(function (action) {
                if (action.isConfirmed) {
                    $.ajax({
                        url: storage_browser_url + 'actions/?path=' + storage_browser_path,
                        type: "POST",
                        data: {
                            action_type: quick_action,
                            names: items
                        },
                        dataType: "json",
                        success: function (data) {
                            if (data.message != undefined && data.message != '') {
                                var message = data.message;

                                if (data.success) {
                                    storageBrowserAjax();
                                    toastr.success(message, trs.messages.success, { timeOut: 10000 });
                                } else {
                                    toastr.error(message, trs.messages.error, { timeOut: 10000 });
                                }
                            }
                        }
                    })
                }
            });
        }
    });

    // View type buttons
    $(document).on('click', '.storage-browser-view-btn', function () {
        var type = $(this).attr('storage-browser-view');

        if (type != undefined && type != '') {
            storage_browser_view_type = type;
            storageBrowserViewtype();
        }
    });

    // Image preview on table
    $(document).on('mouseenter mouseleave', '.storage-browser-list-image-preview', function (e) {
        var url = $(this).attr('storage-browser-list-image-preview');

        if (url != undefined && url != '') {
            var img = $(this).children('img');
            var img_tag = '<img src="' + url + '" alt="image">';

            if (img != undefined && img.length > 0) {

            } else {
                $(this).append(img_tag);
            }

            if (e.type == 'mouseenter') {
                $(this).addClass('hover');
            } else if (e.type == 'mouseleave') {
                $(this).removeClass('hover');
            }
        }
    });

    // Upload area
    $(document).on('change', '#storage-browser-upload-area', function () {
        var files = this.files;
        var label = $('#storage-browser-upload-label');

        if (files != undefined && files.length > 0) {
            var names = [];

            if (files.length > 1) {
                var _msg = trs.messages.stb_files_selected.replaceAll("{count}", files.length);
                label.text(_msg);
            } else {
                for (var i = 0; i < files.length; i++) {
                    names.push(files[i].name);
                }

                label.text(names.join(', '));
            }
        } else {
            label.empty();
        }
    });

    // Submit upload form
    $(document).on('submit', '.storage-browser-upload-form', function (e) {
        e.preventDefault();

        var $this = $(this);
        var $input = $('#storage-browser-upload-area');
        var $label = $('#storage-browser-upload-label');
        var label_name = $label.attr('data-label');
        var $button = $this.find('.storage-browser-action-btn');
        var $message_area = $this.find('.storage-browser-upload-form-msg');
        var $progress_bar = $this.find('#progress-wrp');

        var progress_status_text = 'Compressing...';
        var $progress_block = $progress_bar.children('.progress-bar-in');
        var $progress_status = $progress_bar.children('.progress-bar-status');
        var progress_status_data_text = $progress_status.attr('data-text');

        if (progress_status_data_text != undefined && progress_status_data_text != '') {
            progress_status_text = progress_status_text;
        }

        var file_input = $("#storage-browser-upload-area").get(0);
        var files = file_input.files;

        // Begin form data
        if (files.length > 0) {
            var data = new FormData();
            data.append('action_type', 'upload_file');

            for (var i = 0; i < files.length; i++) {
                data.append(files[i].name, files[i]);
            }

            $.ajax({
                type: "POST",
                url: storage_browser_url + 'actions/?path=' + storage_browser_path,
                contentType: false,
                processData: false,
                data: data,
                dataType: "json",
                beforeSend: function () {
                    $message_area.empty();
                    $button.addClass('with-preloader-icon');
                    $progress_bar.show().removeClass('progress-bar-error');
                    $progress_block.css('width', '0%');
                    $progress_status.text('0%');
                },
                success: function (data) {
                    $button.removeClass('with-preloader-icon');

                    if (data.success) {
                        $input.val('');
                        $label.text(label_name);
                        $progress_status.text(data.message);

                        storageBrowserAjax();
                    } else {
                        $progress_bar.addClass('progress-bar-error');
                        $progress_status.text(trs.messages.error);
                        $message_area.html('<p class="text-danger">' + data.message + '</p>');
                    }

                    if (data.toastr != undefined && data.toastr != '') {
                        if (data.success) {
                            toastr.success(data.toastr, trs.messages.success, { timeOut: 10000 });
                        } else {
                            toastr.error(data.toastr, trs.messages.error, { timeOut: 10000 });
                        }
                    }
                },
                error: function () {
                    alert(ajax_error_msg);
                },
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();

                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt(percentComplete * 100);

                            $progress_block.width(percentComplete + '%');

                            if (percentComplete == 100) {
                                $progress_status.text(progress_status_text);
                            } else {
                                $progress_status.text(percentComplete + '%');
                            }
                        }
                    }, false);

                    return xhr;
                },
            });
        }
    });

    // Run
    storageBrowserRun();
}

function storageBrowserRun() {
    storageBrowserViewtype();
    storageBrowserAjax();
}

function storageBrowserSelect() {
    var selected_icon_attr = 'storage-browser-selected';
    var selected_icon_class = 'ri-checkbox-fill';
    var unselected_icon_class = 'ri-checkbox-blank-line';

    // Select all
    $(document).on('click', '[storage-browser-select-all]', function () {
        var icon = $(this);
        var parent = icon.closest('[storage-browser-block]');
        var checked = icon.attr('storage-browser-select-all');

        if (parent != undefined && parent.length > 0) {
            var items = parent.find('[storage-browser-select]');

            items.each(function () {
                var value = $(this).attr('storage-browser-select');

                if (checked != undefined && checked == 'checked') {
                    icon.attr('storage-browser-select-all', 'none');
                    icon.removeClass(selected_icon_class).addClass(unselected_icon_class);
                    $(this).removeClass(selected_icon_class).addClass(unselected_icon_class);
                    $(this).removeAttr(selected_icon_attr);
                    $(this).closest('tr').removeClass('storage-browser-table-selected');

                } else {
                    icon.attr('storage-browser-select-all', 'checked');
                    icon.removeClass(unselected_icon_class).addClass(selected_icon_class);
                    $(this).removeClass(unselected_icon_class).addClass(selected_icon_class);
                    $(this).attr(selected_icon_attr, value);
                    $(this).closest('tr').addClass('storage-browser-table-selected');
                }
            });

            storageBrowserPopupButtonDisable();
        }
    });

    // Select one
    $(document).on('click', '[storage-browser-select]', function () {
        var $this = $(this);
        var parent = $this.closest('[storage-browser-block]');
        var checked = $this.attr(selected_icon_attr);
        var value = $this.attr('storage-browser-select');

        if (parent != undefined && parent.length > 0) {
            var $item = $('[storage-browser-select="' + value + '"]');
            var all_item = parent.find('[storage-browser-select-all]');

            if (checked != undefined) {
                $item.removeClass(selected_icon_class).addClass(unselected_icon_class);
                $item.removeAttr(selected_icon_attr);
                $item.closest('tr').removeClass('storage-browser-table-selected');
            } else {
                $item.removeClass(unselected_icon_class).addClass(selected_icon_class);
                $item.attr(selected_icon_attr, value);
                $item.closest('tr').addClass('storage-browser-table-selected');
            }

            if (all_item != undefined && all_item.length > 0) {
                var items = parent.find('[storage-browser-select]');
                var items_selected = parent.find('[' + selected_icon_attr + ']');
                var items_count = parseInt(items.length);
                var selected_count = parseInt(items_selected.length);

                if (!isNaN(items_count) && !isNaN(selected_count) && items_count == selected_count) {
                    all_item.attr('storage-browser-select-all', 'checked');
                    all_item.removeClass(unselected_icon_class).addClass(selected_icon_class);
                } else {
                    all_item.attr('storage-browser-select-all', 'none');
                    all_item.removeClass(selected_icon_class).addClass(unselected_icon_class);
                }
            }

            storageBrowserPopupButtonDisable();
        }
    });
}

function storageBrowserAjax(path_name) {
    var data = {};

    var table_tbody = $('.storage-browser-table > tbody');
    var table_tfoot = $('.storage-browser-table > tfoot');

    var grid_items_block = $('.storage-browser-grid-in');
    var grid_notfound = $('.storage-browser-grid-view .storage-browser-table-notfound');

    var popup = $('.storage-browser-info-popup');
    var preloader = $('.storage-browser-list-preloader');

    if (path_name != undefined && path_name != '') {
        data.path = path_name;
    } else {
        data.path = storage_browser_path;
    }

    if (typeof storage_browser_url !== undefined) {
        $.ajax({
            url: storage_browser_url + 'get/',
            type: "GET",
            data: data,
            dataType: "json",
            beforeSend: function () {
                preloader.show();
                table_tfoot.hide();
            },
            success: function (data) {
                table_tbody.empty();
                grid_items_block.empty();
                var files_count = parseInt(data.files_count);

                if (!isNaN(files_count)) {
                    var _msg = trs.messages.stb_files_count.replaceAll("{count}", files_count);
                    $('[storage-browser-text="title"]').text(_msg);

                    if (files_count > 0) {
                        table_tfoot.hide();
                        grid_notfound.hide();
                    } else {
                        table_tfoot.show();
                        grid_notfound.show();
                    }
                }

                if (data.path != undefined && data.path != '') {
                    data.path = data.path.replace(/\/$/, "");
                    storage_browser_path = data.path;
                }

                if (data.folders_list != undefined && data.folders_list != '') {
                    table_tbody.append(data.folders_list);
                }

                if (data.folders_grid != undefined && data.folders_grid != '') {
                    grid_items_block.append(data.folders_grid);
                }

                if (data.files_list != undefined && data.files_list != '') {
                    table_tbody.append(data.files_list);
                }

                if (data.files_grid != undefined && data.files_grid != '') {
                    grid_items_block.append(data.files_grid);
                }

                if (data.infos != undefined && data.infos != '') {
                    popup.html(data.infos);
                }

                if (data.path_list != undefined && data.path_list != '') {
                    var ul = $('.storage-browser-link > ul');
                    ul.empty();

                    $.each(data.path_list, function (path, name) {
                        var li = '<li storage-browser-open-dir="' + path + '">';

                        if (path == '/') {
                            li += '<i class="ri-home-3-line"></i>';
                        }

                        li += name;
                        li += '</li>';

                        ul.append(li);
                    });
                }

                preloader.hide();
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    }
}

function storageBrowserPopup(action, block) {
    var popup = $('.storage-browser-info-popup');
    var item = $('.storage-browser-popup-item');

    if (action != undefined && action == 'show') {
        if (block != undefined && block.length > 0) {
            popup.show();
            block.fadeIn().addClass('animated slideInDown');
        } else {
            alert(ajax_error_msg);
        }
    } else {
        popup.hide();
        item.hide().removeClass('animated slideInDown');
    }
}

function storageBrowserViewtype() {
    $('[storage-browser-view]').removeClass('active');
    $('[storage-browser-view="' + storage_browser_view_type + '"]').addClass('active');
}

function storageBrowserFrame() {
    var div = '<div class="storage-browser-fixed-popup">';
    div += '<div class="storage-browser-fixed-popup-block">';
    div += '<div class="storage-browser-fixed-popup-in">';
    div += '</div>';
    div += '<div class="storage-browser-fixed-popup-btns">';
    div += '<button type="button" storage-browser-fixed-popup-close>' + trs.messages.close + '</button>';
    div += '<button type="button" class="storage-browser-fixed-popup-btn" disabled>' + trs.messages.choose + '</button>';
    div += '</div>';
    div += '</div>';
    div += '</div>';

    $('body').append(div);

    $(document).on('click', '[storage-browser-show]', function () {
        storage_browser_frame_item = $(this);
        storage_browser_frame_id = '';
        storage_browser_frame_action = '';
        storage_browser_frame_select = 'single';

        var $popup = $('.storage-browser-fixed-popup');
        var $block = $('.storage-browser-fixed-popup-in');
        var select_id = $(this).attr('id');
        var select_action = $(this).attr('storage-browser-show');
        var select_type = $(this).attr('storage-browser-select-type');
        var select_path = $(this).attr('storage-browser-path');

        if (select_id != undefined && select_id != '') {
            storage_browser_frame_id = select_id;
        }

        if (select_type != undefined && select_type != '') {
            storage_browser_frame_select = select_type;
        }

        if (select_action != undefined && select_action != '') {
            storage_browser_frame_action = select_action;
        }

        if (select_path != undefined && select_path != '') {
            select_path = select_path.replace(/\/$/, "");
            storage_browser_path = select_path;
        }

        $block.empty();
        $popup.fadeIn();
        $('body').addClass('no-overflow');

        $.get(storage_browser_url + "view", function (data) {
            if (data != undefined && data != '') {
                $('.storage-browser-fixed-popup-in').html(data);

                if (!storage_browser_called) {
                    storageBrowserInit();
                } else {
                    storageBrowserRun();
                }
            }
        });
    });

    $(document).on('click', '[storage-browser-fixed-popup-close]', function () {
        $('.storage-browser-fixed-popup').fadeOut();
        $('.storage-browser-fixed-popup-btn').attr('disabled', 'disabled');
        $('body').removeClass('no-overflow');

        setTimeout(function () {
            $('.storage-browser-fixed-popup-in').empty();
        }, 500);
    });

    $(document).on('click', '.storage-browser-fixed-popup-btn', function () {
        var items = storageBrowserFrameSelectedItems();

        if (storage_browser_frame_item != undefined && storage_browser_frame_item.length > 0) {
            var parent = storage_browser_frame_item.closest('.storage-browser-frame-group');
            var value = parent.find('[storage-browser-value]');

            if (items != undefined && items.length > 0) {
                var objects = Object.assign({}, items);
                var json = JSON.stringify(objects);

                if (storage_browser_frame_action == 'gallery') {
                    parent.find('[stg-gallery--elements="data"]').val(json).trigger('change');
                } else if (storage_browser_frame_select == 'multi') {
                    value.val(json).trigger('change');
                } else {
                    value.val(items[0]).trigger('change');
                }
            }

            $('[storage-browser-fixed-popup-close]').trigger('click');
        } else {
            $('[storage-browser-fixed-popup-close]').trigger('click');
        }

        // Event fire
        storageBrowserEventFire('onItemSelect', {
            'id': storage_browser_frame_id,
            'action': storage_browser_frame_action,
            'button': storage_browser_frame_item,
            'items': items
        });
    });

    $(document).on('click', '.btn-image-clear', function () {
        var parent = $(this).closest('.storage-browser-frame-group');
        var input = parent.find('[storage-browser-input]');
        var value = parent.find('[storage-browser-value]');
        var img_block = parent.find('.storage-browser-input-img');

        if (input != undefined && input.length > 0) {
            input.val('');
            value.val('').trigger('change');
            img_block.empty();
        }
    });

    $(document).on('change paste keyup', '[storage-browser-value]', function () {
        var $this = $(this);
        var value = $this.val();

        storageBrowserItemInit(value, $this);
    });

    $('[storage-browser-value]').each(function () {
        var $this = $(this);
        var value = $this.val();

        storageBrowserItemInit(value, $this);
    });

    $(document).on('click', '[storage-browser-file-open-url]', function () {
        var url = $(this).attr('storage-browser-file-open-url');

        if (url != undefined && url != '') {
            window.open(url, '_blank').focus();
        }
    });

    $(document).on('click', '[storage-browser-file-delete]', function () {
        var objects;
        var url = $(this).attr('storage-browser-file-delete');
        var parent = $(this).closest('.storage-browser-frame-group');
        var input = parent.find('[storage-browser-value]');

        if (confirm(trs.messages.ta_delete_item_qsn)) {
            try {
                objects = JSON.parse(input.val());
            } catch (error) {
                objects = null;
            }
        }

        if (objects != undefined && objects != null) {
            var i = -1;
            var items = {};

            $.each(objects, function (key, value) {
                if (value != url) {
                    i++;
                    items[i] = value;
                }
            });

            var json = JSON.stringify(items);
            input.val(json).trigger('change');
        }
    });
}

function storageBrowserItemInit(value, element) {
    var url;
    var objects;
    var parent = element.closest('.storage-browser-frame-group');
    var input = parent.find('[storage-browser-input]');
    var message_count_file = parent.find('[storage-browser-message="count-files"]').val();
    var message_count_files_selected = parent.find('[storage-browser-message="count-files-selected"]').val();

    if (value != undefined && value != null) {
        var json;

        if (typeof value === 'object' && value !== null) {
            objects = value;
        } else if ($.isArray(value)) {
            objects = Object.assign({}, value);
        } else if (typeof value === 'string') {
            try {
                json = JSON.parse(value);
            } catch (error) {
                json = null;
            }

            if (json != undefined && json != null) {
                objects = json;
            } else {
                url = value;
            }
        }
    }

    // Check object
    if (objects != undefined && objects != null) {
        var count = Object.keys(objects).length;
        var message = '{count} files selected';

        if (message_count_files_selected != undefined && message_count_files_selected != '') {
            message = message_count_files_selected;
        }

        if (count == 1) {
            input.val(Object.values(objects)[0]);
        } else {
            input.val(message.replace('{count}', count));
        }
    } else if (url != '') {
        input.val(url);
    }

    storageBrowserItemPreview(value, objects, element);
}

function storageBrowserItemPreview(value, objects, element) {
    var parent = element.closest('.storage-browser-frame-group');
    var clear_btn = parent.find('.btn-image-clear');

    if (value != undefined && value != '') {
        clear_btn.show();
    } else {
        clear_btn.hide();
    }

    // Clear preview blocks
    parent.find('.storage-browser-input-img').empty();
    parent.find('.storage-browser-input-files').empty();

    if (objects != undefined && objects != null) {
        var count = Object.keys(objects).length;

        if (count == 1) {
            storageBrowserImagePreview(Object.values(objects)[0], element);
        } else {
            var html = '';
            var image_ext = ['jpg', 'jpeg', 'png', 'gif', 'ico'];

            $.each(objects, function (key, url) {
                if (url != undefined && url != '') {
                    var ext = url.substr(url.lastIndexOf('.') + 1);

                    html += '<div class="storage-browser-file-item">';
                    html += '<span class="mr-2">' + url + '</span>';

                    if (ext != undefined && image_ext.includes(ext)) {
                        html += '<i class="ri-zoom-in-line mr-1" storage-browser-file-zoom="file" data-mfp-src="' + url + '"></i>';
                    } else {
                        html += '<i class="ri-external-link-fill mr-1" storage-browser-file-open-url="' + url + '"></i>';
                    }

                    html += '<i class="ri-delete-bin-fill mr-1" storage-browser-file-delete="' + url + '"></i>';
                    html += '</div>';
                }
            });

            parent.find('.storage-browser-input-files').html('<div class="storage-browser-input-files-in">' + html + '</div>');
            $('[storage-browser-file-zoom]').magnificPopup({ type: 'image' });
        }
    } else if (typeof value === 'string' && value != '') {
        storageBrowserImagePreview(value, element);
    }
}

function storageBrowserImagePreview(url, element) {
    var parent = element.closest('.storage-browser-frame-group');
    var type = parent.find('[storage-browser-value]').attr('storage-browser-value');

    if (type != undefined && type == 'image') {
        var allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'ico'];

        if (url != undefined && url != '') {
            var ext = url.substr(url.lastIndexOf('.') + 1);

            if (ext != undefined && allowed_exts.includes(ext)) {
                var html = '<a href="' + url + '" class="storage-browser-image"><img src="' + url + '" alt="image"></a>';
                parent.find('.storage-browser-input-img').html(html);
                $('.storage-browser-image').magnificPopup({ type: 'image' });
            }
        }
    }
}

function storageBrowserEventFire(event_name, data) {
    storage_browser_event_data = data;
    storage_browser_event.trigger(event_name);
}

function storageBrowserEventCallback(event_name, func) {
    $(document).on(event_name, storage_browser_event_id, function () {
        func(storage_browser_event_data);
    });
}

function storageBrowserFrameSelectedItems() {
    var dirs = [];
    var files = [];
    var allowed = false;
    var objects = $('.storage-browser-table').find('[storage-browser-selected]');

    if (objects != undefined && objects.length > 0) {
        objects.each(function () {
            var value = $(this).attr('storage-browser-file-url');

            if (value != undefined && value != '') {
                var type = $(this).attr('storage-browser-select-item');

                if (type != undefined && type == 'file') {
                    files.push(value);
                } else {
                    dirs.push(value);
                }
            }
        });
    }

    if (files.length > 0 && dirs.length < 1) {
        if (storage_browser_frame_select == 'single' && files.length == 1) {
            allowed = true;
        } else if (storage_browser_frame_select == 'multi' && files.length > 0) {
            allowed = true;
        }
    }

    if (allowed) {
        return files;
    } else {
        return false;
    }
}

function storageBrowserPopupButtonDisable() {
    var items = storageBrowserFrameSelectedItems();
    var button = $('.storage-browser-fixed-popup-btn');

    if (items) {
        button.removeAttr('disabled');
    } else {
        button.attr('disabled', 'disabled');
    }
}

function mbUrlParameter(sParam) {
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
}