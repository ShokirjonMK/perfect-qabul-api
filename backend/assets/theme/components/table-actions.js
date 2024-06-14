$(document).ready(function () {
    tableActionsBlockInit();

    $('.card-top-search').submit(function (e) {
        e.preventDefault();
        var input = $(this).find('input[type="text"]');
        var value = input.val();

        if (value != undefined && value != '') {
            gplinkToggleParam('s', value);
        } else {
            gplinkRemoveParam('s');
        }

        window.location.reload();
    });

    $('.card-top-search-clear').click(function () {
        gplinkRemoveParam('s');
        window.location.reload();
    });

    $('[action-btn]').click(function () {
        // Selected items input id
        var selected_items = bulkActionsSelectedItems('#table-selected-items');

        if (selected_items) {
            var type = $(this).attr('action-btn');

            if (type == 'activate') {
                var config = {
                    title: trs.messages.ta_activate_items,
                    text: trs.messages.ta_activate_items_qsn,
                    confirmButtonText: trs.messages.yes,
                    icon: "warning",
                };

                var data = {
                    ajax: 'bulk-actions',
                    action: type,
                    items: selected_items,
                };

                bulkActionsFetch(config, data);
            } else if (type == 'block') {
                var config = {
                    title: trs.messages.ta_block_items,
                    text: trs.messages.ta_block_items_qsn,
                    confirmButtonText: trs.messages.yes,
                    icon: "warning",
                };

                var data = {
                    ajax: 'bulk-actions',
                    action: type,
                    items: selected_items,
                };

                bulkActionsFetch(config, data);
            } else if (type == 'publish') {
                var config = {
                    title: trs.messages.ta_publish_items,
                    text: trs.messages.ta_publish_items_qsn,
                    confirmButtonText: trs.messages.yes,
                    icon: "warning",
                };

                var data = {
                    ajax: 'bulk-actions',
                    action: type,
                    items: selected_items,
                };

                bulkActionsFetch(config, data);
            } else if (type == 'unpublish') {
                var config = {
                    title: trs.messages.ta_unpublish_items,
                    text: trs.messages.ta_unpublish_items_qsn,
                    confirmButtonText: trs.messages.yes,
                    icon: "warning",
                };

                var data = {
                    ajax: 'bulk-actions',
                    action: type,
                    items: selected_items,
                };

                bulkActionsFetch(config, data);
            } else if (type == 'restore') {
                var config = {
                    title: trs.messages.ta_restore_items,
                    text: trs.messages.ta_restore_items_qsn,
                    confirmButtonText: trs.messages.yes,
                    icon: "warning",
                };

                var data = {
                    ajax: 'bulk-actions',
                    action: type,
                    items: selected_items,
                };

                bulkActionsFetch(config, data);
            } else if (type == 'trash') {
                var config = {
                    title: trs.messages.ta_mv_trash_items,
                    text: trs.messages.ta_mv_trash_items_qsn,
                    confirmButtonText: trs.messages.yes,
                    icon: "warning",
                };

                var data = {
                    ajax: 'bulk-actions',
                    action: type,
                    items: selected_items,
                };

                bulkActionsFetch(config, data);
            } else if (type == 'delete') {
                var config = {
                    title: trs.messages.ta_delete_items,
                    text: trs.messages.ta_delete_items_qsn,
                    confirmButtonText: trs.messages.yes_delete,
                    icon: "warning",
                };

                var data = {
                    ajax: 'bulk-actions',
                    action: type,
                    items: selected_items,
                };

                bulkActionsFetch(config, data);
            } else if (type == 'new-order') {
                var config = {
                    title: trs.messages.ta_order_new_items,
                    text: trs.messages.ta_order_new_items_qsn,
                    confirmButtonText: trs.messages.yes,
                    icon: "warning",
                };

                var data = {
                    ajax: 'bulk-actions',
                    action: type,
                    items: selected_items,
                };

                bulkActionsFetch(config, data);
            } else if (type == 'complete-order') {
                var config = {
                    title: trs.messages.ta_order_complete_items,
                    text: trs.messages.ta_order_complete_items_qsn,
                    confirmButtonText: trs.messages.yes,
                    icon: "warning",
                };

                var data = {
                    ajax: 'bulk-actions',
                    action: type,
                    items: selected_items,
                };

                bulkActionsFetch(config, data);
            } else if (type == 'cancel-order') {
                var config = {
                    title: trs.messages.ta_order_cancel_items,
                    text: trs.messages.ta_order_cancel_items_qsn,
                    confirmButtonText: trs.messages.yes,
                    icon: "warning",
                };

                var data = {
                    ajax: 'bulk-actions',
                    action: type,
                    items: selected_items,
                };

                bulkActionsFetch(config, data);
            } else if (type == 'trash-order') {
                var config = {
                    title: trs.messages.delete_order,
                    text: trs.messages.ta_order_mv_trash_items_qsn,
                    confirmButtonText: trs.messages.yes,
                    icon: "warning",
                };

                var data = {
                    ajax: 'bulk-actions',
                    action: type,
                    items: selected_items,
                };

                bulkActionsFetch(config, data);
            } else if (type == 'restore-order') {
                var config = {
                    title: trs.messages.restore_order,
                    text: trs.messages.ta_order_restore_items_qsn,
                    confirmButtonText: trs.messages.yes,
                    icon: "warning",
                };

                var data = {
                    ajax: 'bulk-actions',
                    action: type,
                    items: selected_items,
                };

                bulkActionsFetch(config, data);
            } else if (type == 'delete-order') {
                var config = {
                    title: trs.messages.delete_order,
                    text: trs.messages.ta_order_delete_items_qsn,
                    confirmButtonText: trs.messages.yes_delete,
                    icon: "warning",
                };

                var data = {
                    ajax: 'bulk-actions',
                    action: type,
                    items: selected_items,
                };

                bulkActionsFetch(config, data);
            }
        }
    });

    $(document).on('click', '[ta-single-action]', function () {
        var id = $(this).attr('ta-single-id');
        var type = $(this).attr('ta-single-action');

        if (type == 'activate') {
            var config = {
                title: trs.messages.activate,
                text: trs.messages.ta_activate_item_qsn,
                confirmButtonText: trs.messages.yes,
                icon: "warning",
            };

            var data = {
                ajax: 'bulk-actions',
                action: type,
                id: id,
            };

            bulkActionsFetch(config, data);
        } else if (type == 'block') {
            var config = {
                title: trs.messages.block,
                text: trs.messages.ta_block_item_qsn,
                confirmButtonText: trs.messages.yes,
                icon: "warning",
            };

            var data = {
                ajax: 'bulk-actions',
                action: type,
                id: id,
            };

            bulkActionsFetch(config, data);
        } else if (type == 'publish') {
            var config = {
                title: trs.messages.publish,
                text: trs.messages.ta_publish_item_qsn,
                confirmButtonText: trs.messages.yes,
                icon: "warning",
            };

            var data = {
                ajax: 'bulk-actions',
                action: type,
                id: id,
            };

            bulkActionsFetch(config, data);
        } else if (type == 'unpublish') {
            var config = {
                title: trs.messages.unpublish,
                text: trs.messages.ta_unpublish_item_qsn,
                confirmButtonText: trs.messages.yes,
                icon: "warning",
            };

            var data = {
                ajax: 'bulk-actions',
                action: type,
                id: id,
            };

            bulkActionsFetch(config, data);
        } else if (type == 'restore') {
            var config = {
                title: trs.messages.restore,
                text: trs.messages.ta_restore_item_qsn,
                confirmButtonText: trs.messages.yes,
                icon: "warning",
            };

            var data = {
                ajax: 'bulk-actions',
                action: type,
                id: id,
            };

            bulkActionsFetch(config, data);
        } else if (type == 'trash') {
            var config = {
                title: trs.messages.mv_trash,
                text: trs.messages.ta_mv_trash_item_qsn,
                confirmButtonText: trs.messages.yes,
                icon: "warning",
            };

            var data = {
                ajax: 'bulk-actions',
                action: type,
                id: id,
            };

            bulkActionsFetch(config, data);
        } else if (type == 'delete') {
            var config = {
                title: trs.messages.delete,
                text: trs.messages.ta_delete_item_qsn,
                confirmButtonText: trs.messages.yes_delete,
                icon: "warning",
            };

            var data = {
                ajax: 'bulk-actions',
                action: type,
                id: id,
            };

            bulkActionsFetch(config, data);
        } else if (type == 'new-order') {
            var config = {
                title: trs.messages.ta_order_new_items,
                text: trs.messages.ta_order_new_item_qsn,
                confirmButtonText: trs.messages.yes,
                icon: "warning",
            };

            var data = {
                ajax: 'bulk-actions',
                action: type,
                id: id,
            };

            bulkActionsFetch(config, data);
        } else if (type == 'complete-order') {
            var config = {
                title: trs.messages.complete_order,
                text: trs.messages.ta_order_complete_item_qsn,
                confirmButtonText: trs.messages.yes,
                icon: "warning",
            };

            var data = {
                ajax: 'bulk-actions',
                action: type,
                id: id,
            };

            bulkActionsFetch(config, data);
        } else if (type == 'cancel-order') {
            var config = {
                title: trs.messages.cancel_order,
                text: trs.messages.ta_order_cancel_item_qsn,
                confirmButtonText: trs.messages.yes,
                icon: "warning",
            };

            var data = {
                ajax: 'bulk-actions',
                action: type,
                id: id,
            };

            bulkActionsFetch(config, data);
        } else if (type == 'trash-order') {
            var config = {
                title: trs.messages.delete_order,
                text: trs.messages.ta_order_mv_trash_item_qsn,
                confirmButtonText: trs.messages.yes,
                icon: "warning",
            };

            var data = {
                ajax: 'bulk-actions',
                action: type,
                id: id,
            };

            bulkActionsFetch(config, data);
        } else if (type == 'restore-order') {
            var config = {
                title: trs.messages.restore_order,
                text: trs.messages.ta_order_restore_item_qsn,
                confirmButtonText: trs.messages.yes,
                icon: "warning",
            };

            var data = {
                ajax: 'bulk-actions',
                action: type,
                id: id,
            };

            bulkActionsFetch(config, data);
        } else if (type == 'delete-order') {
            var config = {
                title: trs.messages.delete_order,
                text: trs.messages.ta_order_delete_item_qsn,
                confirmButtonText: trs.messages.yes_delete,
                icon: "warning",
            };

            var data = {
                ajax: 'bulk-actions',
                action: type,
                id: id,
            };

            bulkActionsFetch(config, data);
        }
    });

    $('#quick-action-modal-form').on('submit', function (e) {
        e.preventDefault();

        var data = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: window.location.href,
            data: data,
            dataType: 'json',
            beforeSend: function () {
                pagePreloader('show');
            },
            success: function (data) {
                if (data.message) {
                    alert(data.message);
                } else if (data.error) {
                    alert(ajax_error_msg);
                }

                if (data.success) {
                    window.location.reload();
                } else {
                    pagePreloader('hide');
                }
            },
            error: function () {
                alert(ajax_error_msg);
                pagePreloader('hide');
            },
        });
    });
});

function tableActionsBlockInit() {
    var selected_class = 'ta-select';
    var selected_class_icon = 'ri-checkbox-line';
    var unselected_class_icon = 'ri-checkbox-blank-line';

    $(document).on('click', '[data-ta-select]', function () {
        var $this = $(this);

        if ($this.hasClass(selected_class)) {
            $this.closest('tr').removeClass('tr-selected');

            $this.removeClass(selected_class_icon + ' ' + selected_class)
                .addClass(unselected_class_icon)
                .trigger('classChange');
        } else {
            $this.closest('tr').addClass('tr-selected');

            $this.removeClass(unselected_class_icon)
                .addClass(selected_class_icon + ' ' + selected_class)
                .trigger('classChange');
        }
    });

    $(document).on('classChange', '[data-ta-select]', function () {
        var $this = $(this);
        var $parent = $this.closest('.table-with-actions');

        if ($parent != undefined && $parent.length > 0) {
            var ids = [];
            var $input = $parent.find('[ta-selected-items]');
            var $items = $parent.find('.' + selected_class);

            if ($items != undefined && $items.length > 0) {
                $items.each(function () {
                    var id = $(this).attr('data-ta-select');
                    ids.push(id);
                });
            }

            if (ids.length > 0) {
                $input.val(ids.toString());
            } else {
                $parent.find('[data-ta-select-all]')
                    .removeClass(selected_class_icon)
                    .addClass(unselected_class_icon);

                $input.val('');
            }
        }
    });

    $(document).on('click', '[data-ta-select-all]', function () {
        var $this = $(this);
        var $parent = $this.closest('.table-with-actions');

        if ($parent != undefined && $parent.length > 0) {
            var $items = $parent.find('[data-ta-select]');

            if ($this.hasClass(selected_class_icon)) {
                $this.removeClass(selected_class_icon).addClass(unselected_class_icon);

                $items.each(function () {
                    $(this).closest('tr').removeClass('tr-selected');

                    $(this).removeClass(selected_class_icon + ' ' + selected_class)
                        .addClass(unselected_class_icon)
                        .trigger('classChange');
                });
            } else {
                $this.removeClass(unselected_class_icon).addClass(selected_class_icon);

                $items.each(function () {
                    $(this).closest('tr').addClass('tr-selected');

                    $(this).removeClass(unselected_class_icon)
                        .addClass(selected_class_icon + ' ' + selected_class)
                        .trigger('classChange');
                });
            }
        }
    });
}

function bulkActionsSelectedItems(element) {
    var $element = $(element);

    if ($element != undefined && $element.length > 0) {
        return $element.val();
    }

    return false;
}

function bulkActionsFetch(config, data) {
    var configs = {};
    var datas = {};

    if (typeof config === 'object') {
        configs = config;
    }

    if (typeof data === 'object') {
        datas = data;
    }

    if (configs) {
        Swal.fire({
            title: configs.title,
            text: configs.text,
            icon: configs.icon,
            showCancelButton: !0,
            confirmButtonColor: "#34c38f",
            cancelButtonColor: "#ff3d60",
            confirmButtonText: configs.confirmButtonText
        }).then(function (action) {
            if (action.isConfirmed) {
                bulkActionsAjaxFetch(datas);
            }
        });
    }
}

function bulkActionsAjaxFetch(data) {
    if (data != undefined) {
        $.ajax({
            type: 'POST',
            url: window.location.href,
            data: data,
            dataType: 'json',
            beforeSend: function () {
                pagePreloader('show');
            },
            success: function (data) {
                if (data.message) {
                    alert(data.message);
                } else if (data.error) {
                    alert(ajax_error_msg);
                }

                if (data.success) {
                    window.location.reload();
                } else {
                    pagePreloader('hide');
                }
            },
            error: function () {
                alert(ajax_error_msg);
                pagePreloader('hide');
            },
        });
    } else {
        alert(ajax_error_msg);
    }
}

function tableQuickActionEdit(config) {
    var configs = {};

    if (typeof config === 'object') {
        configs = config;
    }

    if (configs.data != undefined && typeof configs.data === 'object') {
        var id = configs.data.id;

        $.ajax({
            type: 'POST',
            url: window.location.href,
            data: configs.data,
            dataType: 'json',
            beforeSend: function () {
                pagePreloader('show');
            },
            success: function (data) {
                if (data.success && data.item != undefined && data.item != '') {
                    tableActionQuickEditBuild(configs, 'update', id);

                    $.each(data.item, function (key, value) {
                        var item = $('#qe_' + key);

                        if (item != undefined && item.length > 0) {
                            item.val(value);
                        }
                    });
                } else {
                    alert(ajax_error_msg);
                }

                pagePreloader('hide');
            },
            error: function () {
                alert(ajax_error_msg);
                pagePreloader('hide');
            },
        });
    }
}

function tableQuickActionCreate(config) {
    var configs = {};

    if (typeof config === 'object') {
        configs = config;
    }

    if (configs) {
        tableActionQuickEditBuild(configs, 'create');
    }
}

function tableActionQuickEditBuild(configs, type, id) {
    var item_id = 0;
    var action_type = 'create';

    if (type != undefined && type != '') {
        action_type = id;
    }

    if (id != undefined && id != '') {
        item_id = id;
    }

    if (configs) {
        var modal = $('#quick-action-modal');
        var modalDialog = $('#quick-action-modal .modal-dialog');
        var modalTitle = $('#quick-action-modal .modal-title');
        var modalContent = $('#quick-action-modal .modal-body');
        var modalType = $('#quick-action-modal #quick-action-type');
        var modalItemID = $('#quick-action-modal #quick-action-id');

        modalItemID.val(item_id);
        modalType.val(action_type);
        modalContent.empty();
        modalDialog.removeClass('qe-modal-size-large qe-modal-size-x-large');

        if (configs.title != undefined && configs.title != '') {
            modalTitle.html(configs.title);
        }

        if (configs.size != undefined && configs.size != '') {
            modalDialog.addClass('qe-modal-size-' + configs.size);
        }

        if (configs.fields != undefined && configs.fields) {
            var html = '<div class="row">';

            $.each(configs.fields, function (key, object) {
                var id = object.id;
                var col = object.col;
                var type = object.type;
                var name = object.name;
                var required = object.required;

                if (required) {
                    var required_attr = 'required';
                } else {
                    var required_attr = '';
                }

                if (object.height != undefined && object.height != '') {
                    required_attr += ' style="height:' + object.height + 'px;"';
                }

                var label_attr = '<label for="qe_' + id + '">' + name + '</label>';

                html += '<div class="' + col + ' form-group">';

                if (type == 'text' || type == 'email' || type == 'password' || type == 'hidden' || type == 'number') {
                    html += label_attr;
                    html += '<input type="' + type + '" name="' + id + '" id="qe_' + id + '" class="form-control" ' + required_attr + '>';
                }

                if (type == 'textarea') {
                    html += label_attr;
                    html += '<textarea name="' + id + '" id="qe_' + id + '" class="form-control" ' + required_attr + '></textarea>';
                }

                if (type == 'image') {
                    html += label_attr;
                    html += '<div class="input-group">';
                    html += '<input type="text" name="' + id + '" id="qe_' + id + '" class="form-control" ' + required_attr + '>';
                    html += '<div class="input-group-append">';
                    html += '<button type="button" class="btn btn-secondary">' + trs.messages.select + '</button>';
                    html += '</div>';
                    html += '</div>';
                }

                if (type == 'select') {
                    html += label_attr;
                    html += '<select class="form-control custom-select" name="' + id + '" id="qe_' + id + '">';

                    if (object.options != undefined && object.options != '') {
                        $.each(object.options, function (okey, ovalue) {
                            html += '<option value="' + okey + '">' + ovalue + '</option>';
                        });
                    }

                    html += '</select>';
                }

                html += '</div>';
            });

            html += '</div>';

            modalContent.html(html);
        }

        modal.modal('show');
    }
}