var ceditor_elements_init = {};
var ceditor_element_objects = {};
var ceditor_section_objects = {};
var ceditor_event_insert_element = {};
var ceditor_event_render_template = {};

(function ($) {
    var selected_element;
    var selected_section;
    var selected_repeater_block;
    var selected_repeater_element;
    var ceditor_timeout = null;
    var ceditor_trigger_list = [];

    $(document).ready(function () {
        // Tooltip
        $('body').tooltip({ selector: '[data-toggle="tooltip"]' });

        // Init elements
        $.each(ceditor_elements_init, function (i, functionName) {
            functionName();
        });

        // Init block
        $('[ceditor-block]').each(function () {
            var id = $(this).attr('ceditor-block-id');

            if (id != undefined && id.length > 0) {
                var elements = {};
                var sections = {};

                var elements_input = $(this).find('[content-editor-objects="elements"]').val();
                var sections_input = $(this).find('[content-editor-objects="sections"]').val();

                try {
                    elements = JSON.parse(elements_input);
                    sections = JSON.parse(sections_input);
                } catch (error) {
                    elements = null;
                    sections = null;
                }

                ceditor_element_objects[id] = elements;
                ceditor_section_objects[id] = sections;
            }
        });

        // Import elements
        $('[ceditor-block-save-input]').each(function () {
            var $this = $(this);
            var parent = $this.closest('[ceditor-block]');
            var input_name = $this.attr('ceditor-block-save-input');

            if (input_name != undefined && input_name != '') {
                var $ceditor_save_input = $(input_name);

                if ($ceditor_save_input != undefined && $ceditor_save_input.length > 0) {
                    var json = {};
                    var string = $ceditor_save_input.val();

                    try {
                        json = JSON.parse(string);
                    } catch (error) {
                        json = null;
                    }

                    $.when(contentEditorImport(json, parent)).then(function () {
                        parent.attr('ceditor-block', 'enabled');
                        $(document).trigger('contentEditorLoaded');
                    });
                }
            }
        });

        // Init js
        contentEditorSideMenuInit();
        contentEditorSectionsInit();
        contentEditorElementsInit();
    });

    $(window).on('load', function () {
        // Save on changes
        $(document).on('DOMSubtreeModified DOMNodeInserted DOMNodeRemoved', "[ceditor-data]", function () {
            var block = $(this);
            if (ceditor_timeout !== null) {
                clearTimeout(ceditor_timeout);
            }

            ceditor_timeout = setTimeout(function () {
                ceditor_timeout = null;
                contentEditorSave(block);
            }, 500);
        });

        // Save on changes input
        $(document).on('change keyup paste', "[ceditor-data] input, [ceditor-data] textarea, [ceditor-data] select, [ceditor-data] radio", function () {
            var block = $(this).closest('[ceditor-data]');

            if (ceditor_timeout !== null) {
                clearTimeout(ceditor_timeout);
            }

            ceditor_timeout = setTimeout(function () {
                ceditor_timeout = null;
                contentEditorSave(block);
            }, 500);
        });

        // Save form
        $(document).on('submit', '.main-content form', function (e) {
            e.preventDefault();
            var block = $(this).closest('[ceditor-data]');

            clearTimeout(ceditor_timeout);

            $.when(contentEditorSave(block)).then(function () {
                e.currentTarget.submit();
            });
        });
    });

    function contentEditorSectionsInit() {
        var section_modal = '#ceditor-section-settings-modal';
        var section_modal_parent;

        // Insert section
        $(document).on('click', '[ceditor-insert-section]', function () {
            var $this = $(this);
            var parent = $this.closest('[ceditor-block]');
            var section_key = $this.attr('ceditor-insert-section');
            var sections_data = contentEditorGetObjects($this, 'sections');

            if (sections_data != undefined) {
                contentEditorSectionInsert(section_key, sections_data[section_key], parent);
            }
        });

        // Collapse section
        $(document).on('click', '[ceditor-action="collapse-section-item"]', function () {
            var $this = $(this);
            var parent = $this.closest('[ceditor-section-item]');

            if (parent.hasClass('collapsed')) {
                parent.stop().removeClass('collapsed');
            } else {
                parent.stop().addClass('collapsed');
            }
        });

        // Delete section
        $(document).on('click', '[ceditor-action="delete-section-item"]', function () {
            var $this = $(this);
            var parent = $this.closest('[ceditor-section-item]');

            Swal.fire({
                title: trs.messages.delete,
                text: trs.messages.ta_delete_item_qsn,
                icon: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#ff3d60",
                confirmButtonText: trs.messages.yes_delete
            }).then(function (action) {
                if (action.isConfirmed) {
                    parent.remove();
                    contentEditorSectionsDraw();
                }
            });
        });

        // Settings section
        $(document).on('click', '[ceditor-action="settings-section-item"]', function () {
            var $this = $(this);
            var block = $this.closest('[ceditor-block]');
            section_modal_parent = $this.closest('[ceditor-section-item]');

            var modal_html = '';
            var $modal = block.find('[ceditor-section-settings-modal]');
            var $modal_footer = $('body').find(section_modal);

            if ($modal_footer != undefined && $modal_footer.length > 0) {
                $modal_footer.remove();
            }

            modal_html = '<form method="POST" id="ceditor-section-settings-modal">';
            modal_html += $modal.html();
            modal_html += '</form>';

            $('body').append(modal_html);
            $(section_modal + ' > .modal').modal('show').trigger('init');
        });

        // Settings section modal init
        $(document).on('init', section_modal, function () {
            var $this = $(this);

            if (section_modal_parent != undefined && section_modal_parent.length > 0) {
                var $item = section_modal_parent.find('[content-editor-section]');

                if ($item != undefined && $item.length > 0) {
                    var id_name = $item.attr('id');
                    var class_name = $item.attr('class');

                    if (id_name != undefined && id_name != '') {
                        $this.find('[name="ceditor-section-settings-id"]').val(id_name);
                    }

                    if (class_name != undefined && class_name != '') {
                        $this.find('[name="ceditor-section-settings-class"]').val(class_name);
                    }

                    var attrs = [];
                    var exclude_attrs = ['class', 'id', 'content-editor-section'];

                    $.each($item[0].attributes, function () {
                        if (this.specified) {
                            if ($.inArray(this.name, exclude_attrs) != -1) {
                                return true;
                            } else {
                                attrs.push(this.name + '=' + '"' + this.value + '"');
                            }
                        }
                    });

                    if (attrs.length > 0) {
                        $this.find('[name="ceditor-section-settings-attrs"]').val(attrs.join(" "));
                    }
                } else {
                    console.log('Content editor error: Section item not found.');
                }
            } else {
                console.log('Content editor error: Selected section not found.');
            }
        });

        // Settings section modal submit
        $(document).on('submit', section_modal, function (e) {
            e.preventDefault();
            var $this = $(this);
            var $modal = $this.children('.modal');

            if (section_modal_parent != undefined && section_modal_parent.length > 0) {
                var $item = section_modal_parent.find('[content-editor-section]');

                if ($item != undefined && $item.length > 0) {
                    var data = $this.serializeArray();

                    $.each(data, function (i, item) {
                        var value = item.value;

                        if (value != undefined) {
                            var key = item.name;

                            if (key == 'ceditor-section-settings-id') {
                                value != '' ? $item.attr('id', value) : $item.removeAttr('id');
                            } else if (key == 'ceditor-section-settings-class') {
                                value != '' ? $item.attr('class', value) : $item.removeAttr('class');
                            } else if (key == 'ceditor-section-settings-attrs') {
                                var attrs = [];
                                var allowed = ['class', 'id', 'content-editor-section'];

                                if (value != '') {
                                    var temp = document.createElement("div");
                                    temp.innerHTML = "<div " + value + "></div>";
                                    attrs = temp.firstElementChild.attributes;
                                }

                                if (attrs.length > 0) {
                                    $.each(attrs, function (k, attr) {
                                        if ($.inArray(attr.name, allowed) != -1) {
                                            return true;
                                        } else if (attr.name != undefined && attr.name != '') {
                                            allowed.push(attr.name);
                                            $item.attr(attr.name, attr.value);
                                        }
                                    });
                                }

                                $.each($item[0].attributes, function () {
                                    if (this.specified) {
                                        if ($.inArray(this.name, allowed) != -1) {
                                            return true;
                                        } else {
                                            $item.removeAttr(this.name);
                                        }
                                    }
                                });
                            }
                        }
                    });
                } else {
                    console.log('Content editor error: Section item not found.');
                }
            } else {
                console.log('Content editor error: Selected section not found.');
            }

            $modal.modal('hide');
        });

        // Section title change event
        $(document).on('change keyup paste', '.content-editor-card-title', function () {
            var $this = $(this);
            var value = $this.val();
            var $block = $this.closest('[ceditor-section-item]');
            var data = $block.attr('ceditor-section-item');
            var config = JSON.parse(data);

            if (config != undefined && config != '') {
                value = value.replace(/<[^>]*>?/gm, '');
                config.title = value;

                $block.attr('ceditor-section-item', JSON.stringify(config));
            }
        });

        // Sortable section
        $('[ceditor-data]').sortable({
            handle: '.content-editor-card-top',
            animation: 150,
            onChange: function (event) {
                contentEditorSectionsDraw();
            }
        });
    }

    function contentEditorSectionsDraw() {
        $('[ceditor-data]').each(function () {
            var $this = $(this);
            var childs = $this.children('[ceditor-section-item]');

            if (childs != undefined && childs.length > 0) {
                var x = 0;

                childs.each(function () {
                    x++;
                    var data = $(this).attr('ceditor-section-item');
                    var type = $(this).attr('content-editor-section-type');
                    var config = JSON.parse(data);

                    if (config != undefined && config != '') {
                        $(this).find('.content-editor-card-title').val(config.title);
                    }

                    if (type != undefined && (type == 'group' || type == 'repeater')) {
                        $(this).find('[ceditor-side-menu-open="elements"]').parent().remove();
                    }

                    $(this).find('.content-editor-card-index').text('#' + x);
                });
            }
        });
    }

    function contentEditorSectionInsert(section_key, sections_data, parent) {
        var $html;
        var time = new Date().getTime();
        var section_type = 'standard';

        if (sections_data.group_elements != undefined && sections_data.group_elements !== null) {
            section_type = 'group';
        } else if (sections_data.repeater_elements != undefined && sections_data.repeater_elements !== null) {
            section_type = 'repeater';
        }

        if (sections_data != undefined) {
            var section_config = {};
            var $section_block_html = parent.find('[ceditor-section-block]').children();

            if (sections_data.section_config != undefined) {
                section_config = sections_data.section_config;
            } else {
                section_config = {
                    'title': sections_data.title,
                    'type': section_key,
                };
            }

            if ($section_block_html != undefined && $section_block_html.length > 0) {
                section_html = sections_data.html;

                if (section_html != undefined && section_html != '') {
                    var $section = $(section_html);
                    $section.attr('content-editor-section', section_key);

                    var $clone_section_block_html = $section_block_html.clone();
                    $clone_section_block_html.attr('content-editor-section-key', section_key);
                    $clone_section_block_html.attr('content-editor-section-type', section_type);
                    $clone_section_block_html.attr('ceditor-section-item', JSON.stringify(section_config));
                    $clone_section_block_html.attr('data-id', time);
                    $clone_section_block_html.find('[content-editor-html]').html($section);

                    $html = $clone_section_block_html;
                } else {
                    console.log('Content editor error: Section html not found.');
                }
            } else {
                console.log('Content editor error: Section block item not found.');
            }

            if ($html != undefined && $html.length > 0) {
                var sidemenu = parent.find('[ceditor-side-menu]');

                if (selected_section != undefined && selected_section.length > 0) {
                    selected_section.after($html);
                } else {
                    parent.children('[ceditor-data]').append($html);
                }

                sidemenu.trigger('hide');
                contentEditorSectionsDraw();
                contentEditorSectionTypeInit($html, section_key, sections_data, parent);

                // Init elements
                var section_elements = sections_data.elements;

                if (section_type == 'standard' && section_elements != undefined && section_elements != null) {
                    selected_section = $html;
                    var elements_list = contentEditorGetObjects($html, 'elements');

                    $.each(section_elements, function (key, item) {
                        var object = null;
                        var element_key = item.key;
                        var element_type = item.type;

                        if (element_type == 'group') {
                            var group_elements = {};
                            var group_elements_all = {};
                            var group_elements_list = $.extend(true, {}, elements_list[element_key]);
                            var group_elements_all = group_elements_list.elements;

                            if (item.items != undefined) {
                                var group_objs = $.extend(true, group_elements_all, item.items);

                                $.each(group_objs, function (item_key, item_data) {
                                    var item_object = contentEditorSectionElementValues(group_elements_all, item_key, item_data);

                                    if (item_object != undefined && item_object !== null) {
                                        group_elements[item_key] = item_object;
                                    }
                                });
                            }

                            if (group_elements != undefined && group_elements !== null) {
                                object = $.extend(true, {}, group_elements_list);
                                object.elements = group_elements;
                            }
                        } else if (element_type == 'repeater') {
                            var repeater_elements = [];
                            var repeater_elements_list = $.extend(true, {}, elements_list[element_key]);
                            var repeater_elements_all = repeater_elements_list.elements;

                            if (item.items != undefined) {
                                $.each(item.items, function (i, values) {
                                    var repeater_items = {};
                                    var repeater_objs = $.extend(true, repeater_elements_all, values);

                                    $.each(repeater_objs, function (item_key, item_data) {
                                        var item_object = contentEditorSectionElementValues(repeater_elements_all, item_key, item_data);

                                        if (item_object != undefined && item_object !== null) {
                                            repeater_items[item_key] = item_object;
                                        }
                                    });

                                    repeater_elements.push(repeater_items);
                                });
                            }

                            if (repeater_elements != undefined && repeater_elements !== null) {
                                object = $.extend(true, {}, repeater_elements_list);
                                object.items = repeater_elements;
                            }
                        } else {
                            object = contentEditorSectionElementValues(elements_list, element_key, item);
                        }

                        if (object != undefined && object !== null) {
                            contentEditorElementInsert(element_key, object, parent);
                        }
                    });
                }
            }
        }
    }

    function contentEditorSectionTypeInit(section, section_key, sections_data, parent) {
        var html_item = '';
        var html_block_attrs = '';

        if (sections_data.group_elements != undefined && sections_data.group_elements !== null) {
            var elements_html = '';
            var group_elements = sections_data.group_elements;
            var elements = $.extend(true, {}, sections_data.elements);

            if (elements != undefined && elements !== null) {
                var objects = {};
                var items = $.extend(true, group_elements, elements.items);

                $.each(items, function (key, item) {
                    var item_object = contentEditorSectionElementValues(group_elements, key, item);

                    if (item_object != undefined && item_object !== null) {
                        objects[key] = item_object;
                    }
                });

                if (objects != undefined && objects != null) {
                    group_elements = objects;
                }
            }

            $.each(group_elements, function (key, item) {
                elements_html += contentEditorElementInsertSingle(key, 'group', item, parent, true);
            });

            if (elements_html != undefined && elements_html != '') {
                html_item += elements_html;
            }

            html_block_attrs = ' ceditor-element-type="group"';
        }

        if (sections_data.repeater_elements != undefined && sections_data.repeater_elements !== null) {
            var elements_html = '';
            var elements = sections_data.elements;
            var repeater = sections_data.repeater_elements;
            var $add_btn = parent.find('[ceditor-add-element-repeat-btn]').clone();

            if (elements != undefined && elements.items != undefined && elements.items.length > 0) {
                $.each(elements.items, function (i, item) {
                    var output = '';
                    var objects = $.extend(true, item, repeater.elements);

                    $.each(objects, function (key, item) {
                        var item_object = contentEditorSectionElementValues(repeater.elements, key, item);

                        if (item_object != undefined && item_object !== null) {
                            output += contentEditorElementInsertSingle(key, 'repeater', item_object, parent, true);
                        }
                    });

                    if (output != undefined && output != '') {
                        elements_html += '<div ceditor-element-repeater="' + section_key + '">';
                        elements_html += output;
                        elements_html += '</div>';
                    }
                });
            } else if (repeater.elements != undefined && repeater.elements !== null) {
                var output = '';

                $.each(repeater.elements, function (key, item) {
                    output += contentEditorElementInsertSingle(key, 'repeater', item, parent, true);
                });

                if (output != undefined && output != '') {
                    elements_html += '<div ceditor-element-repeater="' + section_key + '">';
                    elements_html += output;
                    elements_html += '</div>';
                }
            }

            if (elements_html != undefined && elements_html != '') {
                html_item += '<div class="ceditor-element-repeater-block">';
                html_item += elements_html;
                html_item += '</div>';

                if ($add_btn != undefined && $add_btn.length > 0) {
                    if (repeater.button_text != undefined && repeater.button_text != '') {
                        $add_btn.find('[ceditor-action="element-repeat"]').children('span').html(repeater.button_text);
                    }

                    html_item += $add_btn.html();
                }
            }

            html_block_attrs = ' ceditor-element-type="repeater"';
        }

        if (html_item != undefined && html_item != '') {
            var html_block = '<div ceditor-element-block="' + section_key + '"' + html_block_attrs + '>';
            html_block += html_item;
            html_block += '</div>';

            section.find('[content-editor-section]').append(html_block);

            contentEditorElementsDraw();
            contentEditorElementsTrigger(section);
        }
    }

    function contentEditorSectionElementValues(elements_list, element_key, data) {
        var object;

        if (elements_list != undefined && elements_list[element_key] != undefined && elements_list[element_key] !== null) {
            var element_type = data.type;
            object = $.extend(true, {}, elements_list[element_key]);

            if (data.value != undefined) {
                var special_inputs = ['checkbox', 'radio', 'select'];

                if (element_type == 'input' && special_inputs.includes(object.input_type)) {
                    object.data.default = data.value;
                } else if (object.html != undefined) {
                    object.html = data.value;
                } else {
                    object.default = data.value;
                }
            }
        }

        return object;
    }

    function contentEditorElementsInit() {
        // Insert element
        $(document).on('click', '[ceditor-insert-element]', function () {
            var $this = $(this);
            var parent = $this.closest('[ceditor-block]');
            var element_key = $this.attr('ceditor-insert-element');
            var elements_data = contentEditorGetObjects($this, 'elements');

            if (elements_data != undefined) {
                contentEditorElementInsert(element_key, elements_data[element_key], parent);
            }
        });

        // Repeat element
        $(document).on('click', '[ceditor-action="element-repeat"]', function () {
            var $this = $(this);
            var parent = $this.closest('[ceditor-block]');
            var section = $this.closest('[ceditor-section-item]');
            var section_type = section.attr('content-editor-section-type');
            var element = $this.closest('[ceditor-element-block]');

            if (section_type != undefined && section_type == 'standard') {
                var element_key = element.attr('ceditor-element-block');
                var elements_data = contentEditorGetObjects($this, 'elements');
                var elements = elements_data[element_key];
            } else if (section_type != undefined && section_type == 'repeater') {
                var element_key = section.attr('content-editor-section-key');
                var sections_data = contentEditorGetObjects($this, 'sections');
                var elements = sections_data[element_key];
            }

            if (elements != undefined) {
                selected_repeater_element = null;
                selected_repeater_block = element.find('.ceditor-element-repeater-block');
                contentEditorElementInsertRepeater(element_key, elements, parent);
            }
        });

        // Add repeater element down
        $(document).on('click', '[ceditor-action="add-repeater-element-down"]', function () {
            var $this = $(this);
            var parent = $this.closest('[ceditor-block]');
            var section = $this.closest('[ceditor-section-item]');
            var section_type = section.attr('content-editor-section-type');
            var element = $this.closest('[ceditor-element-block]');
            var repeater = $this.closest('[ceditor-element-repeater]');

            if (section_type != undefined && section_type == 'standard') {
                var element_key = element.attr('ceditor-element-block');
                var elements_data = contentEditorGetObjects($this, 'elements');
                var elements = elements_data[element_key];
            } else if (section_type != undefined && section_type == 'repeater') {
                var element_key = section.attr('content-editor-section-key');
                var sections_data = contentEditorGetObjects($this, 'sections');
                var elements = sections_data[element_key];
            }

            if (elements != undefined) {
                selected_repeater_block = null;
                selected_repeater_element = repeater;
                contentEditorElementInsertRepeater(element_key, elements, parent);
            }
        });

        // Move element up
        $(document).on('click', '[ceditor-action="element-up"]', function () {
            var $this = $(this);
            var $block = $this.closest('[ceditor-element-block]');
            var $repeater = $this.closest('[ceditor-element-repeater]');

            if ($repeater != undefined && $repeater.length > 0) {
                var $element = $repeater;
                var $prev = $block.prev();
                var prev_attr = $prev.attr('ceditor-element-repeater');
            } else {
                var $element = $block;
                var $prev = $block.prev();
                var prev_attr = $prev.attr('ceditor-element-block');
            }

            if (prev_attr != undefined) {
                $element.insertBefore($prev);
                var offset = $element.offset();

                if (!isNaN(offset.top)) {
                    $("html, body").animate({ scrollTop: (offset.top - 100) }, 500);
                }
            }
        });

        // Move element down
        $(document).on('click', '[ceditor-action="element-down"]', function () {
            var $this = $(this);
            var $block = $this.closest('[ceditor-element-block]');
            var $repeater = $this.closest('[ceditor-element-repeater]');

            if ($repeater != undefined && $repeater.length > 0) {
                var $element = $repeater;
                var $next = $repeater.next();
                var next_attr = $repeater.attr('ceditor-element-repeater');
            } else {
                var $element = $block;
                var $next = $block.next();
                var next_attr = $next.attr('ceditor-element-block');
            }

            if (next_attr != undefined) {
                $element.insertAfter($next);
                var offset = $element.offset();

                if (!isNaN(offset.top)) {
                    $("html, body").animate({ scrollTop: (offset.top - 100) }, 500);
                }
            }
        });

        // Delete element
        $(document).on('click', '[ceditor-action="element-delete"]', function () {
            var $this = $(this);
            var $block = $this.closest('[ceditor-element-block]');
            var $repeater = $this.closest('[ceditor-element-repeater]');

            if ($repeater != undefined && $repeater.length > 0) {
                var $delete = $repeater;
            } else {
                var $delete = $block;
            }

            Swal.fire({
                title: trs.messages.delete,
                text: trs.messages.ta_delete_item_qsn,
                icon: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#ff3d60",
                confirmButtonText: trs.messages.yes_delete
            }).then(function (action) {
                if (action.isConfirmed) {
                    $delete.remove();
                }
            });
        });
    }

    function contentEditorElementInsert(element_key, data, parent) {
        var type = '';
        var html_item = '';
        var html_block = '';
        var html_block_attrs = '';

        // Clear trigger list
        ceditor_trigger_list = [];

        if (data != undefined && data !== null) {
            var title = '';
            var elements;
            var is_single = true;

            if (typeof data.elements === 'object' && data.elements !== null) {
                elements = data.elements;
            }

            if (data.type != undefined && data.type != '') {
                type = data.type;
            }

            if (data.title != undefined && data.title != '') {
                title = data.title;
            }

            if (type == 'group') {
                is_single = false;
                html_block_attrs = ' ceditor-element-type="group"';

                if (elements != undefined && elements !== null) {
                    var elements_html = '';

                    $.each(elements, function (key, item) {
                        elements_html += contentEditorElementInsertSingle(key, 'group', item, parent, true);
                    });

                    if (elements_html != undefined && elements_html != '') {
                        html_item = '';

                        if (title != '') {
                            html_item += '<div class="h4 ceditor-block-label">' + title + '</div>';
                        }

                        html_item += elements_html;
                    }
                }
            }

            if (type == 'repeater') {
                is_single = false;
                html_block_attrs = ' ceditor-element-type="repeater"';

                if (elements != undefined && elements !== null) {
                    var elements_html = '';
                    var $add_btn = parent.find('[ceditor-add-element-repeat-btn]').clone();

                    if (data.items != undefined && data.items != null) {
                        $.each(data.items, function (key, repeater_item) {
                            var repeater_html = '';

                            $.each(repeater_item, function (key, item) {
                                repeater_html += contentEditorElementInsertSingle(key, 'repeater', item, parent, true);
                            });

                            if (repeater_html != undefined && repeater_html != '') {
                                elements_html += '<div ceditor-element-repeater="' + element_key + '">';
                                elements_html += repeater_html;
                                elements_html += '</div>';
                            }
                        });
                    } else {
                        var repeater_html = '';

                        $.each(elements, function (key, item) {
                            repeater_html += contentEditorElementInsertSingle(key, 'repeater', item, parent, true);
                        });

                        if (repeater_html != undefined && repeater_html != '') {
                            elements_html = '<div ceditor-element-repeater="' + element_key + '">';
                            elements_html += repeater_html;
                            elements_html += '</div>';
                        }
                    }

                    if (elements_html != undefined && elements_html != '') {
                        html_item = '';

                        if (title != '') {
                            html_item += '<div class="h4 ceditor-block-label">' + title + '</div>';
                        }

                        html_item += '<div class="ceditor-element-repeater-block">';
                        html_item += elements_html;
                        html_item += '</div>';

                        if ($add_btn != undefined && $add_btn.length > 0) {
                            if (data.button_text != undefined && data.button_text != '') {
                                $add_btn.find('[ceditor-action="element-repeat"]').children('span').html(data.button_text);
                            }

                            html_item += $add_btn.html();
                        }
                    }
                }
            }

            if (is_single) {
                html_item = contentEditorElementInsertSingle(element_key, 'single', data, parent);
                html_block_attrs = ' ceditor-element-type="single"';
            }
        }

        if (html_item != undefined && html_item != '') {
            var $block;
            var section_html;
            var sidemenu = parent.find('[ceditor-side-menu]');

            // Prepeare html
            html_block += '<div ceditor-element-block="' + element_key + '"' + html_block_attrs + '>';
            html_block += html_item;
            html_block += '</div>';

            if (selected_section != undefined && selected_section.length > 0) {
                section_html = selected_section.find('[content-editor-section]');
            }

            if (selected_element != undefined && selected_element.length > 0) {
                selected_element.after(html_block);
                $block = selected_element;
            } else if (section_html != undefined && section_html.length > 0) {
                section_html.append(html_block);
                $block = section_html;
            }

            sidemenu.trigger('hide');
            contentEditorElementsDraw();
            contentEditorElementsTrigger($block);
        }
    }

    function contentEditorElementInsertRepeater(element_key, data, parent) {
        var html_item = '';

        // Clear trigger list
        ceditor_trigger_list = [];

        if (data != undefined && data !== null) {
            var elements;

            if (data.repeater_elements != undefined && data.repeater_elements !== null) {
                data.elements = data.repeater_elements.elements;
            }

            if (typeof data.elements === 'object' && data.elements !== null) {
                elements = data.elements;

                if (elements != undefined && elements !== null) {
                    var elements_html = '';

                    $.each(elements, function (key, item) {
                        elements_html += contentEditorElementInsertSingle(key, 'group', item, parent, true);
                    });

                    if (elements_html != undefined && elements_html != '') {
                        html_item += '<div ceditor-element-repeater="' + element_key + '">';
                        html_item += elements_html;
                        html_item += '</div>';
                    }
                }
            }
        }

        if (html_item != undefined && html_item != '') {
            var $block;
            var offset_top = 0;

            if (selected_repeater_element != undefined && selected_repeater_element.length > 0) {
                selected_repeater_element.after(html_item);
                $block = selected_repeater_element;

                var offset = $block.offset();
                var block_height = $block.outerHeight();
                offset_top = block_height + (offset.top - 100);
            } else if (selected_repeater_block != undefined && selected_repeater_block.length > 0) {
                selected_repeater_block.append(html_item);
                $block = selected_repeater_block;
                var $last_block = $block.find('[ceditor-element-repeater]:last-child');

                var offset = $last_block.offset();
                offset_top = (offset.top - 100);
            }

            // Scroll to element
            if (!isNaN(offset_top)) {
                $("html, body").animate({ scrollTop: offset_top }, 500);
            }

            contentEditorElementsDraw();
            contentEditorElementsTrigger($block);
        }
    }

    function contentEditorElementInsertSingle(element_key, element_type, data, parent) {
        var html = '';
        var attrs = '';
        var attrs_in = '';
        var type = '';
        var item_data_html = '';

        if (data.type != undefined && data.type != '') {
            type = data.type;
        }

        // With label
        var with_label = true;
        var disable_label_types = ['audio', 'html', 'social_media', 'tinymce', 'video'];
        var disable_label_templates = ['gallery_block', 'image_block'];

        if (disable_label_types.includes(type)) {
            with_label = false;
        }

        if (type == 'input') {
            var data_input_items = {};
            var data_input_values_attrs = '';
            var data_input_values_class = '';
            var data_input_default_value = '';

            if (data.data != undefined && data.data !== null) {
                var data_input_values = data.data;

                if (data_input_values.items != undefined) {
                    data_input_items = data_input_values.items;
                }

                if (data_input_values.attrs != undefined) {
                    data_input_values_attrs = data_input_values.attrs;
                }

                if (data_input_values.class != undefined) {
                    data_input_values_class = data_input_values.class;
                }

                if (data_input_values.default != undefined) {
                    data_input_default_value = data_input_values.default;
                }
            }

            // Get input type
            var data_input_type = 'text';

            if (data.input_type != undefined && data.input_type != '') {
                data_input_type = data.input_type;
            }

            // Set attr
            attrs += ' ceditor-element-input-type="' + data_input_type + '"';

            // Get input attrs
            var data_input_attrs = '';

            if (data.input_attrs != undefined && data.input_attrs != '') {
                data_input_attrs = data.input_attrs;
            }

            // Get input class
            var data_input_class = '';

            if (data.input_class != undefined && data.input_class != '') {
                data_input_class = data.input_class;
            }

            // Get input defalut value
            var data_input_default = '';

            if (data.default != undefined && data.default != '') {
                data_input_default = data.default;
            }

            // Custom input types
            var custom_input_with_items = ['checkbox', 'radio', 'select'];

            if ($.inArray(data_input_type, custom_input_with_items) != -1) {
                var x = 0;
                var div_attrs = {};
                var custom_input_html = '';
                var time = new Date().valueOf();
                var custom_input_name = 'ceditor-input-' + element_key + '-' + data_input_type + '-' + time;

                // Create items attr
                if (data_input_values_attrs != '') {
                    var temp = document.createElement("div");
                    temp.innerHTML = "<div " + data_input_values_attrs + "></div>";
                    div_attrs = temp.firstElementChild.attributes;
                }

                if (data_input_type == 'select') {
                    item_data_html = '<select class="' + data_input_values_class + '" ' + data_input_attrs + ' content-editor-element-item-data="' + data_input_type + '">';

                    $.each(data_input_items, function (i, value) {
                        var attrs = data_input_values_attrs;

                        if (attrs != '') {
                            attrs = attrs.replace('{value}', i);
                            attrs = attrs.replace('{ value }', i);
                        }

                        if ($.isArray(data_input_default_value) && $.inArray(i, data_input_default_value) != -1) {
                            attrs += ' selected';
                        } else if (data_input_default_value != '' && data_input_default_value == i) {
                            attrs += ' selected';
                        }

                        custom_input_html += '<option value="' + i + '" ' + attrs + '>' + value + '</option>';
                    });

                    item_data_html += custom_input_html;
                    item_data_html += '</select>';
                } else {
                    var render_template = parent.find('[ceditor-render-element-template="' + data_input_type + '"]');

                    if (data_input_type == 'checkbox') {
                        custom_input_name = custom_input_name + '[]';
                    }

                    if (render_template != undefined && render_template.length > 0) {
                        $.each(data_input_items, function (i, value) {
                            x++;
                            var clone = render_template.clone();
                            var id = custom_input_name + '-' + x;

                            var attrs = {
                                'id': id,
                                'name': custom_input_name,
                            };

                            if (data_input_values_class != '') {
                                clone.find('.ceditor-input').addClass(data_input_values_class);
                            }

                            if ($.isArray(data_input_default_value) && $.inArray(i, data_input_default_value) != -1) {
                                attrs.checked = 'checked';
                            } else if (data_input_default_value != '' && data_input_default_value == i) {
                                attrs.checked = 'checked';
                            }

                            if (div_attrs.length > 0) {
                                $.each(div_attrs, function (k, attr) {
                                    var attr_value = attr.value;

                                    if (attr_value != '') {
                                        attr_value = attr_value.replace('{value}', i);
                                        attr_value = attr_value.replace('{ value }', i);
                                    }

                                    if (attr.name != undefined && attr.name != '') {
                                        attrs[attr.name] = attr_value;
                                    }
                                });
                            }

                            clone.find('.ceditor-input').val(i).attr(attrs);
                            clone.find('.ceditor-label').html(value).attr('for', id);
                            custom_input_html += clone.html();
                        });

                        item_data_html += custom_input_html;
                    }
                }
            } else if (data_input_type == 'color') {
                var render_template = parent.find('[ceditor-render-element-template="' + data_input_type + '"]');

                if (render_template != undefined && render_template.length > 0) {
                    var $clone = render_template.clone();
                    var time = new Date().valueOf();

                    if (data_input_default != '') {
                        $clone.find('.ceditor-input').val(data_input_default);
                        $clone.find('.ceditor-colorpicker-block').attr('data-color', data_input_default);
                    }

                    $clone.find('.ceditor-colorpicker-block').attr('ceditor-color-picker', time);
                    item_data_html = $clone.html();
                }
            } else if (data_input_type == 'textarea') {
                item_data_html = '<textarea class="' + data_input_class + '" ' + data_input_attrs + ' content-editor-element-item-data="' + data_input_type + '">';

                if (data.default != undefined && data.default != '') {
                    item_data_html += data.default;
                }

                item_data_html += '</textarea>';
            } else {
                var item_input_value = '';

                if (data.default != undefined && data.default != '') {
                    item_input_value = 'value="' + data.default + '"';
                }

                item_data_html = '<input type="' + data_input_type + '" class="' + data_input_class + '" ' + data_input_attrs + ' content-editor-element-item-data="' + data_input_type + '" ' + item_input_value + '/>';
            }
        } else if (data.render_template != undefined && data.render_template != '') {
            var render_template = parent.find('[ceditor-render-element-template="' + data.render_template + '"]');

            if (render_template != undefined && render_template.length > 0) {
                var $clone = render_template.clone();
                $clone = ceditor_event_render_template.init(element_key, type, data, $clone, element_type, parent);

                $.each(ceditor_event_render_template, function (key, funcName) {
                    if (type == key) {
                        $clone = funcName(element_key, type, data, $clone, element_type, parent);
                    }
                });

                if ($clone != undefined && $clone.length > 0) {
                    item_data_html = $clone.html();
                }
            }

            if (disable_label_templates.includes(data.render_template)) {
                with_label = false;
            }
        } else if (data.html != undefined && data.html != '') {
            item_data_html = data.html;
        }

        if (type == 'tinymce') {
            var tinymce_type = 'text';

            if (data.tinymce != undefined && data.tinymce != '') {
                tinymce_type = data.tinymce;
            }

            attrs_in = ' ceditor-tinymce="' + tinymce_type + '"';
        }

        if (item_data_html != '' && type != '') {
            html += '<div ceditor-element-item="' + type + '" ceditor-element-key="' + element_key + '"' + attrs + '>';

            if (with_label || element_type == 'group' || element_type == 'repeater') {
                html += '<label class="ceditor-element-label">' + data.title + '</label>';
            }

            html += '<div ceditor-element="' + type + '"' + attrs_in + '>';
            html += item_data_html;
            html += '</div>';
            html += '<textarea style="display:none;" class="d-none" ceditor-element-data-json="' + element_key + '">' + JSON.stringify(data) + '</textarea>';
            html += '</div>';
        }

        ceditor_trigger_list.push(type);

        return html;
    }

    function contentEditorElementsDraw() {
        var elements = [];

        $('[ceditor-data]').each(function () {
            var $blocks = $(this).find('[ceditor-element-block]');

            if ($blocks != undefined && $blocks.length > 0) {
                $blocks.each(function () {
                    var $this = $(this);
                    var type = $this.attr('ceditor-element-type');

                    if (type != undefined && type != '') {
                        var $items = $this.children('[ceditor-element-item]');
                        var $repeater = $this.find('[ceditor-element-repeater]');

                        if (type == 'group') {
                            elements.push($this);
                        } else if (type == 'repeater') {
                            elements.push($this);

                            if ($repeater != undefined && $repeater.length > 0) {
                                $repeater.each(function () {
                                    elements.push($(this));
                                });
                            }
                        } else if (type == 'single') {
                            elements.push($items);
                        }
                    }
                });
            }
        });

        if (elements != undefined && elements.length > 0) {
            $.each(elements, function (i, element) {
                if (element != undefined && element.length > 0) {
                    var set_actions = true;
                    var $parent = element.closest('[ceditor-block]');
                    var $section = element.closest('[ceditor-section-item]');
                    var $actions_block = element.children('.ceditor-element-actions');
                    var $actions_block_default = $parent.find('[ceditor-element-actions]').clone();
                    var $actions_block_extra = element.find('[ceditor-element-extra-buttons]');
                    var repeater = element.attr('ceditor-element-repeater');
                    var element_type = element.attr('ceditor-element-type');
                    var section_type = $section.attr('content-editor-section-type');

                    if (section_type != undefined && section_type == 'group') {
                        set_actions = false;
                    } else if (element_type != undefined && element_type == 'repeater' && section_type != undefined && section_type == 'repeater') {
                        set_actions = false;
                    } else if ($actions_block != undefined && $actions_block.length > 0) {
                        set_actions = false;
                    }

                    if (set_actions) {
                        if (repeater != undefined && repeater != '') {
                            $actions_block_default.find('[ceditor-action="add-element-down"]').attr('ceditor-action', 'add-repeater-element-down');
                        }

                        var html = '<div class="ceditor-element-actions">' + $actions_block_default.html() + '</div>';
                        element.append(html);
                        element.addClass('ceditor-element-actions-block');
                    }

                    if ($actions_block_extra != undefined && $actions_block_extra.length > 0) {
                        $actions_block_extra.each(function () {
                            var $parent = $(this).closest('[ceditor-element-item]');
                            var $btn_block = $parent.children('.ceditor-element-actions');
                            var $btn_block_ex = $parent.children('.ceditor-element-actions-ex');
                            var $btn_block_init = $parent.children('.ceditor-element-actions-init');

                            if ($btn_block_ex != undefined && $btn_block_ex.length > 0) {
                                return true;
                            } else if ($btn_block_init != undefined && $btn_block_init.length > 0) {
                                return true;
                            } else if ($btn_block != undefined && $btn_block.length > 0) {
                                $btn_block.addClass('ceditor-element-actions-init');
                                $btn_block.children('.content-editor-element-top-actions').prepend($(this).html());
                            } else {
                                var div = '<div class="ceditor-element-actions ceditor-element-actions-ex ceditor-element-actions-init">';
                                div += '<div class="content-editor-element-top-actions">';
                                div += $(this).html();
                                div += '</div>';
                                div += '</div>';
                                $parent.append(div);
                                $parent.addClass('ceditor-element-actions-block ceditor-element-actions-block-ex');
                            }
                        });
                    }
                }
            });
        }
    }

    function contentEditorElementsTrigger($block) {
        if (ceditor_trigger_list != undefined && ceditor_trigger_list.length > 0) {
            $.each(ceditor_trigger_list, function (i, name) {
                ceditor_event_insert_element.init(name, $block);

                $.each(ceditor_event_insert_element, function (key, funcName) {
                    if (name == key) {
                        funcName(name, $block);
                    }
                });

                $(document).trigger('content_editor_insert_element_' + name);
            });
        }
    }

    function contentEditorSideMenuInit() {
        // Side menu show
        $(document).on('show', '[ceditor-side-menu]', function () {
            $(this).stop().removeClass('active hide--menu').addClass('active').fadeIn(0);
        });

        // Side menu hide
        $(document).on('hide', '[ceditor-side-menu]', function () {
            selected_element = '';
            selected_section = '';

            $(this).stop().removeClass('active hide--menu').addClass('hide--menu').fadeOut(300);
        });

        // Side menu close button
        $(document).on('click', '.content-editor-side-menu-close', function () {
            var $this = $(this);
            var block = $this.closest('[ceditor-side-menu]');

            block.trigger('hide');
        });

        // Sections block show
        $(document).on('click', '[ceditor-side-menu-open="sections"]', function () {
            var $this = $(this);
            var parent = $this.closest('[ceditor-block]');
            var sidemenu = parent.find('[ceditor-side-menu]');

            selected_section = '';
            sidemenu.trigger('hide');
            contentEditorSideMenu(parent, 'show', 'sections');
        });

        // Add section button
        $(document).on('click', '[ceditor-section-action="add-down"]', function () {
            var $this = $(this);
            var parent = $this.closest('[ceditor-block]');
            var sidemenu = parent.find('[ceditor-side-menu]');

            sidemenu.trigger('hide');
            selected_section = $this.closest('[ceditor-section-item]');
            contentEditorSideMenu(parent, 'show', 'sections');
        });

        // Elements block show
        $(document).on('click', '[ceditor-side-menu-open="elements"]', function () {
            var $this = $(this);
            var parent = $this.closest('[ceditor-block]');
            var sidemenu = parent.find('[ceditor-side-menu]');

            selected_element = '';
            sidemenu.trigger('hide');
            selected_section = $this.closest('[ceditor-section-item]');
            contentEditorSideMenu(parent, 'show', 'elements');
        });

        // Add element down
        $(document).on('click', '[ceditor-action="add-element-down"]', function () {
            var $this = $(this);
            var parent = $this.closest('[ceditor-block]');
            var sidemenu = parent.find('[ceditor-side-menu]');
            var $block = $this.closest('[ceditor-element-block]');

            sidemenu.trigger('hide');
            selected_element = $block;
            selected_section = $this.closest('[ceditor-section-item]');
            contentEditorSideMenu(parent, 'show', 'elements');
        });
    }

    function contentEditorSideMenu(parent, action, type) {
        if (action == 'hide') {
            var block = parent.find('[ceditor-side-menu]');
            block.trigger('hide');
        } else if (action == 'show') {
            var block = parent.find('[ceditor-side-menu="' + type + '"]');

            if (block != undefined && block.length > 0) {
                block.trigger('show');
            }
        }
    }

    var contentEditorGetObjects = function (item, type) {
        var parent = item.closest('[ceditor-block]');
        var parent_id = parent.attr('ceditor-block-id');

        if (parent_id != undefined && parent_id != '') {
            var elements = ceditor_element_objects[parent_id];
            var sections = ceditor_section_objects[parent_id];

            if (type == 'sections') {
                return sections;
            }

            if (type == 'elements') {
                return elements;
            }
        }
    }

    function contentEditorImport(json, parent) {
        if (typeof json === 'object' && json !== null) {
            $.each(json, function (i, section) {
                var section_key = section.section_key;
                var section_attrs = section.section_attrs;
                var sections_data = contentEditorGetObjects(parent, 'sections');

                if (sections_data != undefined) {
                    var section_data = sections_data[section_key];

                    if (section_data != undefined && section_data !== null) {
                        var section_merge = $.extend(true, section, section_data);
                        section_merge.html = '<div ' + section_attrs + '></div>';
                        contentEditorSectionInsert(section_key, section_merge, parent);
                    } else {
                        console.log('Content editor error: Undefined section type. [' + section_key + ']');
                    }
                }
            });
        }
    }

    function contentEditorSave(ceditor_block) {
        if (ceditor_block != undefined && ceditor_block.length > 0) {
            var ceditor_output_data = [];
            var parent = ceditor_block.parent();
            var ceditor_save = parent.attr('ceditor-block');

            if (ceditor_save != undefined && ceditor_save == 'enabled') {
                var $sections = ceditor_block.find('[ceditor-section-item]');
                var save_input = ceditor_block.attr('ceditor-block-save-input');

                $sections.each(function () {
                    var $this = $(this);
                    var section_attrs = $this.attr('ceditor-section-item');
                    var section_type = $this.attr('content-editor-section-type');
                    var $init = $this.find('[content-editor-section]');
                    var $blocks = $this.find('[ceditor-element-block]');
                    var section_config = JSON.parse(section_attrs);

                    var config = {
                        elements: [],
                        section_key: '',
                        section_attrs: "",
                        section_attrs_list: {},
                        section_config: {},
                    };

                    if (section_config != undefined && section_config != '') {
                        config.section_key = section_config.type;
                        config.section_config = section_config;
                    }

                    if ($init != undefined && $init.length > 0) {
                        var attrs = [];
                        var exclude_attrs = ['content-editor-section'];

                        $.each($init[0].attributes, function () {
                            if (this.specified) {
                                if ($.inArray(this.name, exclude_attrs) != -1) {
                                    return true;
                                } else {
                                    config.section_attrs_list[this.name] = this.value;
                                    attrs.push(this.name + '=' + '"' + this.value + '"');
                                }
                            }
                        });

                        if (attrs.length > 0) {
                            config.section_attrs = attrs.join(" ");
                        }
                    }

                    if ($blocks != undefined && $blocks.length > 0) {
                        var first_element;
                        var elements = contentEditorSaveElements($blocks);

                        if (elements != undefined && elements.length > 0) {
                            first_element = elements[0];
                        }

                        if (section_type == 'group') {
                            config.elements = first_element;
                        } else if (section_type == 'repeater') {
                            config.elements = first_element;
                        } else {
                            config.elements = elements;
                        }
                    }

                    ceditor_output_data.push(config);
                });

                if (save_input != undefined && save_input != '') {
                    var $save_input = $(save_input);

                    if ($save_input != undefined && $save_input.length > 0) {
                        $save_input.val(JSON.stringify(ceditor_output_data, null, 4));
                    }
                }

                console.log('Saving...');
            }
        }
    }

    function contentEditorSaveElements($blocks) {
        var blocks = [];

        $blocks.each(function () {
            var $this = $(this);
            var element_items = {};
            var element_type = $this.attr('ceditor-element-type');
            var element_key = $this.attr('ceditor-element-block');

            if (element_type != undefined) {
                var is_group = false;
                var is_repeater = false;
                var is_single = false;

                if (element_type == 'group') {
                    is_group = true;

                    var items_list = {};
                    var $elements = $this.children('[ceditor-element-item]');

                    if ($elements != undefined && $elements.length > 0) {
                        $elements.each(function () {
                            var $this = $(this);
                            var item_key = $this.attr('ceditor-element-key');
                            var item_type = $this.attr('ceditor-element-item');

                            var item_data = contentEditorSaveElementItem(item_key, item_type, $this, element_type);

                            if (typeof item_data === 'object' && item_data !== null) {
                                items_list[item_key] = item_data;
                            }
                        });
                    }

                    if (items_list != undefined && items_list !== null && !$.isEmptyObject(items_list)) {
                        blocks.push({
                            'key': element_key,
                            'type': element_type,
                            'items': items_list
                        });
                    }
                } else if (element_type == 'repeater') {
                    is_repeater = true;
                    var $repeater = $this.find('[ceditor-element-repeater]');

                    if ($repeater != undefined && $repeater.length > 0) {
                        var repeater_array = [];

                        $repeater.each(function () {
                            var objects = {};
                            var $this = $(this);
                            var $repeater_items = $this.children('[ceditor-element-item]');

                            if ($repeater_items != undefined && $repeater_items.length > 0) {
                                $repeater_items.each(function () {
                                    var $this = $(this);
                                    var item_key = $this.attr('ceditor-element-key');
                                    var item_type = $this.attr('ceditor-element-item');

                                    var item_data = contentEditorSaveElementItem(item_key, item_type, $this, element_type);

                                    if (typeof item_data === 'object' && item_data !== null) {
                                        objects[item_key] = item_data;
                                    }
                                });
                            }

                            if (objects != undefined && objects !== null && !$.isEmptyObject(objects)) {
                                repeater_array.push(objects);
                            }
                        });

                        if (repeater_array != undefined && repeater_array.length > 0) {
                            blocks.push({
                                'key': element_key,
                                'type': element_type,
                                'items': repeater_array
                            });
                        }
                    }
                } else if (element_type == 'single') {
                    is_single = true;
                    var $elements = $this.children('[ceditor-element-item]');

                    if ($elements != undefined && $elements.length > 0) {
                        var item_key = $elements.attr('ceditor-element-key');
                        var item_type = $elements.attr('ceditor-element-item');

                        element_items = contentEditorSaveElementItem(item_key, item_type, $elements, element_type);
                    }
                }

                if (element_items != undefined && element_items !== null && !$.isEmptyObject(element_items)) {
                    blocks.push(element_items);
                }
            }
        });

        return blocks;
    }

    function contentEditorSaveElementItem(item_key, item_type, item, element_type) {
        var data;
        var run = false;
        var $element;

        if (item_key != undefined && item_key != '') {
            run = true;
        }

        if (run && item_type != undefined && item_type != '') {
            run = true;
        }

        if (run && item != undefined && item.length > 0) {
            run = true;
            $element = item.children('[ceditor-element]');
        }


        if (run && $element != undefined && $element.length > 0) {
            data = {
                'key': item_key,
                'type': item_type,
                'value': '',
            };

            // Get data input
            var $data_input = $element.find('[content-editor-element-item-data]');

            if (item_type == 'html') {
                data.value = $element.html();
            } else if (item_type == 'input') {
                var input_type = item.attr('ceditor-element-input-type');

                if (input_type == 'checkbox') {
                    var selected = [];

                    $element.find('[content-editor-element-item-data]:checked').each(function () {
                        selected.push($(this).val());
                    });

                    data.value = selected;
                } else if (input_type == 'radio') {
                    var $input = $element.find('[content-editor-element-item-data]:checked');
                    data.value = $input.val();
                } else {
                    data.value = $data_input.val();
                }
            } else if (item_type == 'tinymce') {
                var content = '';
                var element_block_id = $element.attr('id');

                if (element_block_id != undefined && element_block_id != '') {
                    var tiny = tinymce.get(element_block_id);

                    if (tiny != undefined) {
                        content = tiny.getContent();
                    }
                }

                data.value = content;
            } else if ($data_input != undefined && $data_input.length > 0) {
                var object;
                var value = $data_input.val();

                try {
                    object = JSON.parse(value);
                } catch (error) {
                    object = null;
                }

                if (object != undefined && object !== null) {
                    var x = 0;
                    var array = [];
                    var object_count = Object.keys(object).length;

                    $.each(object, function (key, value) {
                        if (!isNaN(key)) {
                            x++;
                        }

                        array.push(value);
                    });

                    if (x == object_count) {
                        data.value = array;
                    } else {
                        data.value = object;
                    }
                } else {
                    data.value = value;
                }
            }
        }

        return data;
    }
})(jQuery);

function contentEditorTinymceInit() {
    var items = $('[ceditor-tinymce]');

    if (items != undefined && items.length > 0) {
        items.each(function () {
            var $this = $(this);
            var element = $this.closest('[ceditor-element-item]');
            var placeholder = $this.attr('data-placeholder');

            var config = {
                inline: true,
                contextmenu: false,
                language: site_lang,
            };

            if (element != undefined && element.length > 0) {
                var run = true;
                var element_type = $this.attr('ceditor-tinymce');

                if (placeholder != undefined && placeholder != '') {
                    config.placeholder = placeholder;
                }

                if (this.id != undefined && this.id != '') {
                    run = false;
                }

                if (run) {
                    var time = new Date().valueOf();
                    var id = 'ceditor-element-' + element_type + '-id-' + time;

                    // Set id
                    $this.attr('id', id);

                    // Get config
                    if (element_type == 'heading') {
                        config.menubar = false;
                        config.plugins = ['paste autolink link code emoticons'];
                        config.toolbar = 'formatselect | bold italic underline formatgroup | alignleft aligncenter alignright alignjustify | link unlink | emoticons | removeformat insertgroup';
                        config.toolbar_groups = {
                            formatgroup: {
                                icon: 'chevron-down',
                                tooltip: '',
                                items: 'fontsizeselect | forecolor backcolor | strikethrough superscript subscript'
                            },
                            insertgroup: {
                                icon: 'plus',
                                tooltip: '',
                                items: 'undo redo | searchreplace fullscreen code | help'
                            }
                        };
                        config.block_formats = 'Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6';
                        config.valid_elements = 'h1[*],h2[*],h3[*],h4[*],h5[*],h6[*],a[*],span[*],em[*],strong[*],b[*],sub[*],sup[*],br[*]';
                        config.force_br_newlines = true
                        config.force_p_newlines = false;
                        config.forced_root_block = 'h2';
                    } else if (element_type == 'figcaption') {
                        config.menubar = false;
                        config.plugins = ['paste autolink link code emoticons'];
                        config.toolbar = 'bold italic underline formatgroup | alignleft aligncenter alignright alignjustify | link unlink | emoticons | removeformat insertgroup';
                        config.toolbar_groups = {
                            formatgroup: {
                                icon: 'chevron-down',
                                tooltip: '',
                                items: 'fontsizeselect | forecolor backcolor | strikethrough superscript subscript'
                            },
                            insertgroup: {
                                icon: 'plus',
                                tooltip: '',
                                items: 'undo redo | searchreplace fullscreen code | help'
                            }
                        };
                        config.valid_elements = 'p[*],a[*],span[*],em[*],strong[*],b[*],sub[*],sup[*],br[*]';
                        config.forced_root_block = 'ul';
                    } else if (element_type == 'list') {
                        config.menubar = false;
                        config.plugins = ["advlist autolink link lists print preview hr anchor pagebreak searchreplace code fullscreen help nonbreaking table directionality paste image media emoticons"];
                        config.toolbar = "bold italic underline formatgroup | alignleft aligncenter alignright alignjustify | link unlink | bullist numlist | outdent indent | emoticons image | removeformat insertgroup";
                        config.toolbar_groups = {
                            formatgroup: {
                                icon: 'chevron-down',
                                tooltip: '',
                                items: 'fontsizeselect | forecolor backcolor | strikethrough superscript subscript'
                            },
                            insertgroup: {
                                icon: 'plus',
                                tooltip: '',
                                items: 'undo redo | searchreplace fullscreen code | help'
                            }
                        };
                        config.valid_elements = 'ol[*],ul[*],li[*],img[*],p[*],strong[*],sub[*],sup[*],em[*],span[*],a[*],br[*],hr[*]';
                        config.setup = function (editor) {
                            editor.on('NodeChange change', function (e) {
                                var content = '';

                                if (editor.getContent() != undefined) {
                                    content = editor.getContent();
                                }

                                if (content != '') {
                                    var $tag = $(content);

                                    if ($tag != undefined && $tag.length > 0) {
                                        var string = '';
                                        var valid = true;

                                        $tag.each(function () {
                                            var $this = $(this);
                                            var html = $this.html();

                                            if (html != undefined) {
                                                var outer_html = $this[0].outerHTML;

                                                if ($this.is('ul') || $this.is('ol')) {
                                                    string += html;
                                                } else {
                                                    valid = false;
                                                    string += '<li>' + outer_html + '</li>';
                                                }
                                            }
                                        });

                                        if (!valid) {
                                            editor.setContent('<ul>' + string + '</ul>');
                                        }
                                    }
                                }
                            });
                        }
                    } else if (element_type == 'quote') {
                        config.menubar = false;
                        config.plugins = ["advlist autolink link lists print preview hr anchor pagebreak searchreplace code fullscreen help nonbreaking table directionality paste image media emoticons"];
                        config.toolbar = "bold italic underline formatgroup | alignleft aligncenter alignright alignjustify | link unlink | emoticons image | removeformat insertgroup";
                        config.toolbar_groups = {
                            formatgroup: {
                                icon: 'chevron-down',
                                tooltip: '',
                                items: 'fontsizeselect | forecolor backcolor | strikethrough superscript subscript'
                            },
                            insertgroup: {
                                icon: 'plus',
                                tooltip: '',
                                items: 'undo redo | searchreplace fullscreen code | help'
                            }
                        };
                        config.valid_elements = 'blockquote[*],img[*],p[*],strong[*],sub[*],sup[*],em[*],span[*],a[*],br[*]';
                        config.forced_root_block = 'blockquote';
                    } else if (element_type == 'table') {
                        config.menubar = false;
                        config.plugins = ["advlist autolink link lists print preview hr anchor pagebreak searchreplace code fullscreen help nonbreaking table directionality paste image media emoticons"];
                        config.toolbar = "bold italic underline formatgroup | alignleft aligncenter alignright alignjustify | link unlink | emoticons image table | removeformat insertgroup";
                        config.toolbar_groups = {
                            formatgroup: {
                                icon: 'chevron-down',
                                tooltip: '',
                                items: 'fontsizeselect | forecolor backcolor | strikethrough superscript subscript'
                            },
                            insertgroup: {
                                icon: 'plus',
                                tooltip: '',
                                items: 'undo redo | searchreplace fullscreen code | help'
                            }
                        };
                        config.valid_elements = 'table[*],thead[*],tbody[*],tfoot[*],th[*],td[*],tr[*],img[*],p[*],strong[*],sub[*],sup[*],em[*],span[*],a[*],br[*]';
                    } else {
                        config.menubar = false;
                        config.plugins = ["advlist autolink link lists print preview hr anchor pagebreak searchreplace code fullscreen help nonbreaking table directionality paste image media emoticons"];
                        config.toolbar = "formatselect | bold italic underline formatgroup | alignleft aligncenter alignright alignjustify | link unlink | emoticons image table paragraphgroup | removeformat insertgroup";
                        config.toolbar_groups = {
                            formatgroup: {
                                icon: 'chevron-down',
                                tooltip: '',
                                items: 'fontsizeselect | forecolor backcolor | strikethrough superscript subscript'
                            },
                            paragraphgroup: {
                                icon: 'image-options',
                                tooltip: '',
                                items: 'blockquote bullist numlist | outdent indent | media hr pagebreak'
                            },
                            insertgroup: {
                                icon: 'plus',
                                tooltip: '',
                                items: 'undo redo | searchreplace fullscreen code | help'
                            }
                        };
                        config.block_formats = 'Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6; Paragraph=p; Preformatted=pre';
                    }

                    // Tinymce
                    config.selector = '#' + id;
                    tinymce.init(config);
                }
            }
        });
    }
}