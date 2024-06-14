ceditor_elements_init.audio = function () {
    $(document).on('change', '[content-editor-element-item-data="audio"]', function () {
        var $this = $(this);
        var url = $this.val();
        var parent = $this.closest('[ceditor-element-item]');
        var element = $this.closest('[ceditor-element]');
        var configs_block = element.find('[ceditor-element-configs]');
        var item_block = element.find('[ceditor-element-template-item]');
        var top_buttons = parent.find('.content-editor-element-top-actions');

        if (url != undefined && url != '') {
            var template_block = configs_block.find('[ceditor-element-template-block]').clone();
            template_block.find('source').attr('src', url);
            item_block.html(template_block.html());

            configs_block.addClass('d-none');
            top_buttons.children('.ceditor-element-audio-btn-add').addClass('d-none');
            top_buttons.children('.ceditor-element-audio-btn-replace').removeClass('d-none');
            top_buttons.children('.ceditor-element-audio-btn-clear').removeClass('d-none');
        } else {
            item_block.empty();
            configs_block.removeClass('d-none');

            top_buttons.children('.ceditor-element-audio-btn-add').removeClass('d-none');
            top_buttons.children('.ceditor-element-audio-btn-replace').addClass('d-none');
            top_buttons.children('.ceditor-element-audio-btn-clear').addClass('d-none');
        }
    });

    $(document).on('click', '.ceditor-element-audio-btn-clear', function () {
        var $this = $(this);
        var element = $this.closest('[ceditor-element-item]');
        var input = element.find('[content-editor-element-item-data]');

        input.val('');
        input.trigger('change');
    });

    // Storage browser event
    storageBrowserEventCallback('onItemSelect', function (data) {
        if (data.action == 'content-editor-audio') {
            var items = data.items;
            var button = data.button;
            var parent = button.closest('[ceditor-element-item]');

            if (items != undefined && items.length > 0) {
                var url = items[0];
                var input = parent.find('[content-editor-element-item-data]');

                input.val(url);
                input.trigger('change');
            }
        }
    });
}

ceditor_elements_init.button = function () {
    $(document).on('change', '[content-editor-element-item-data="button"]', function () {
        var object;
        var $this = $(this);
        var value = $this.val();
        var element = $this.closest('[ceditor-element]');

        try {
            object = JSON.parse(value);
        } catch (error) {
            object = null;
        }

        if (object != undefined && object !== null) {
            var button_link = '';
            var button_text = '';

            if (object.link != undefined && object.link) {
                button_link = object.link;
            }

            if (object.text != undefined && object.text) {
                button_text = object.text;
            }

            element.find('[ceditor-element-input-button="text"]').attr('value', button_text);
            element.find('[ceditor-element-input-button="link"]').attr('value', button_link);
        }
    });

    $(document).on('change keyup', '[ceditor-element-input-button]', function () {
        var $this = $(this);
        var element = $this.closest('[ceditor-element]');
        var data_input = element.children('[content-editor-element-item-data]');

        if (data_input != undefined && data_input.length > 0) {
            var button_data = {
                'text': element.find('[ceditor-element-input-button="text"]').val(),
                'link': element.find('[ceditor-element-input-button="link"]').val(),
            }

            data_input.val(JSON.stringify(button_data)).trigger('change');
        }
    });
}

ceditor_elements_init.image_block = function () {
    var image_data = {
        'url': '',
        'figcaption': '',
    };

    $(document).on('change', '[content-editor-element-item-data="image_block"]', function () {
        var url = '';
        var data = {};
        var $this = $(this);
        var value = $this.val();
        var parent = $this.closest('[ceditor-element-item]');
        var element = $this.closest('[ceditor-element]');
        var configs_block = element.find('[ceditor-element-configs]');
        var item_block = element.find('[ceditor-element-template-item]');
        var top_buttons = parent.find('.content-editor-element-top-actions');

        try {
            data = JSON.parse(value);
        } catch (error) {
            data = null;
        }

        if (data != undefined && data !== null) {
            url = data.url;
        }

        if (url != undefined && url != '') {
            var template_block = configs_block.find('[ceditor-element-template-block]').clone();
            template_block.find('img').attr('src', url);
            template_block.find('figcaption').attr({
                'ceditor-tinymce': 'figcaption'
            });

            if (data.figcaption != undefined && data.figcaption != '') {
                template_block.find('figcaption').html(data.figcaption);
            }

            item_block.html(template_block.html());
            contentEditorTinymceInit();

            configs_block.addClass('d-none');
            top_buttons.children('.ceditor-element-image-block-btn-add').addClass('d-none');
            top_buttons.children('.ceditor-element-image-block-btn-replace').removeClass('d-none');
            top_buttons.children('.ceditor-element-image-block-btn-clear').removeClass('d-none');
        } else {
            item_block.empty();
            configs_block.removeClass('d-none');

            top_buttons.children('.ceditor-element-image-block-btn-add').removeClass('d-none');
            top_buttons.children('.ceditor-element-image-block-btn-replace').addClass('d-none');
            top_buttons.children('.ceditor-element-image-block-btn-clear').addClass('d-none');
        }
    });

    $(document).on('DOMSubtreeModified DOMNodeInserted DOMNodeRemoved', '[ceditor-element-template-item="image_block"] figcaption', function () {
        var object;
        var content = '';
        var $this = $(this);

        var element = $this.closest('[ceditor-element]');
        var data_input = element.children('[content-editor-element-item-data]');

        if (this.id != undefined && this.id != '') {
            var tiny = tinymce.get(this.id);

            if (tiny != undefined) {
                content = tiny.getContent();
            }
        }

        if (data_input != undefined && data_input.length > 0) {
            try {
                object = JSON.parse(data_input.val());
            } catch (error) {
                object = null;
            }
        }

        if (object != undefined && object !== null) {
            object.figcaption = content;
            data_input.val(JSON.stringify(object));
        }
    });

    $(document).on('click', '.ceditor-element-image-block-btn-clear', function () {
        var $this = $(this);
        var element = $this.closest('[ceditor-element-item]');
        var input = element.find('[content-editor-element-item-data]');

        input.val('');
        input.trigger('change');
    });

    // Storage browser event
    storageBrowserEventCallback('onItemSelect', function (data) {
        if (data.action == 'content-editor-image-block') {
            var items = data.items;
            var button = data.button;
            var parent = button.closest('[ceditor-element-item]');

            if (items != undefined && items.length > 0) {
                image_data.url = items[0];
                var input = parent.find('[content-editor-element-item-data]');

                input.val(JSON.stringify(image_data));
                input.trigger('change');
            }
        }
    });
}

ceditor_elements_init.gallery_block = function () {
    $(document).on('change', '[content-editor-element-item-data="gallery_block"]', function () {
        var array = [];
        var $this = $(this);
        var parent = $this.closest('[ceditor-element-item]');
        var element = $this.closest('[ceditor-element]');
        var configs_block = element.find('[ceditor-element-configs]');
        var item_block = element.find('[ceditor-element-template-item]');
        var top_buttons = parent.find('.content-editor-element-top-actions');

        if (item_block != undefined && item_block.length > 0) {
            var $image_input = item_block.find('[ceditor-element-gallery-image]');

            if ($image_input != undefined && $image_input.length > 0) {
                $image_input.each(function () {
                    var image_url = $(this).attr('src');

                    if (image_url != undefined && image_url != '') {
                        array.push(image_url);
                    }
                });
            }

            // Sortable js
            item_block.sortable({
                handle: '.content-editor-gallery-image',
                animation: 150,
                onChange: function (evt) {
                    var $block = $(evt.from);
                    var $parent = $block.closest('[ceditor-element]');

                    $parent.children('[content-editor-element-item-data="gallery_block"]').trigger('change');
                }
            });
        }

        if (array != undefined && array.length > 0) {
            $(this).val(JSON.stringify(array));

            configs_block.addClass('d-none');
            top_buttons.children('[ceditor-element-gallery-block-btn="clear"]').removeClass('d-none');
        } else {
            $(this).val('');

            configs_block.removeClass('d-none');
            top_buttons.children('[ceditor-element-gallery-block-btn="clear"]').addClass('d-none');
        }
    });

    $(document).on('change', '[content-editor-element-gallery-items="data"]', function () {
        var images;
        var $this = $(this);
        var value = $this.val();
        var parent = $this.closest('[ceditor-element-item]');
        var element = $this.closest('[ceditor-element]');
        var item_block = element.find('[ceditor-element-template-item]');
        var data_input = element.find('[content-editor-element-item-data="gallery_block"]');

        try {
            images = JSON.parse(value);
        } catch (error) {
            images = '';
        }

        if (images != undefined && images != null) {
            $.each(images, function (i, image_url) {
                var template_block = parent.find('[ceditor-element-template-block]').clone();
                template_block.find('img').attr('src', image_url);
                item_block.append(template_block.html());
            });
        }

        $this.val('');
        data_input.trigger('change');
    });

    $(document).on('click', '[ceditor-element-gallery-block-btn="remove-item"]', function () {
        var $this = $(this);
        var $element = $this.closest('[ceditor-element]');
        var $block = $this.closest('.content-editor-gallery-image');
        var $data_input = $element.find('[content-editor-element-item-data="gallery_block"]');

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
                $block.remove();
                $data_input.trigger('change');
            }
        });
    });

    $(document).on('click', '[ceditor-element-gallery-block-btn="clear"]', function () {
        var $this = $(this);
        var element = $this.closest('[ceditor-element-item]');
        var item_block = element.find('[ceditor-element-template-item]');
        var input = element.find('[content-editor-element-item-data]');

        item_block.empty();
        input.val('').trigger('change');
    });

    // Storage browser event
    storageBrowserEventCallback('onItemSelect', function (data) {
        if (data.action == 'content-editor-gallery-block') {
            var items = data.items;
            var button = data.button;
            var parent = button.closest('[ceditor-element-item]');

            if (items != undefined && items.length > 0) {
                var input = parent.find('[content-editor-element-gallery-items="data"]');

                input.val(JSON.stringify(items));
                input.trigger('change');
            }
        }
    });
}

ceditor_elements_init.social_media = function () {
    var ceditor_selected_element_social_media;
    var ceditor_element_social_media_modal_block = '#ceditor-element-social-media-modal';

    $(document).on('change', '[content-editor-element-item-data="social_media"]', function () {
        var $this = $(this);
        var code = $this.val();
        var type_input = $this.parent().children('[content-editor-element-social-media-type]');
        var type = type_input.attr('content-editor-element-social-media-type');

        var parent = $this.closest('[ceditor-element-item]');
        var item_block = parent.find('[ceditor-element-template-item]');
        var configs_block = parent.find('[ceditor-element-configs]');
        var top_buttons = parent.find('.content-editor-element-top-actions');

        // Check api script tag
        var timeout = 0;

        if (type == 'vk') {
            var script = ceditor_social_media_vk_api();

            if (!script) {
                timeout = 1000;
            }
        }

        if (code != '') {
            setTimeout(function () {
                item_block.empty();
                item_block.html(code);

                if (type == 'instagram' && window.instgrm != undefined) {
                    window.instgrm.Embeds.process();
                }

                if (type == 'twitter' && typeof twttr !== 'undefined') {
                    twttr.widgets.load();
                }
            }, timeout);

            configs_block.addClass('d-none');
            top_buttons.children('[ceditor-element-social-media-btn="add"]').addClass('d-none');
            top_buttons.children('[ceditor-element-social-media-btn="replace"]').removeClass('d-none');
            top_buttons.children('[ceditor-element-social-media-btn="clear"]').removeClass('d-none');
        } else {
            item_block.empty();
            configs_block.removeClass('d-none');

            top_buttons.children('[ceditor-element-social-media-btn="add"]').removeClass('d-none');
            top_buttons.children('[ceditor-element-social-media-btn="replace"]').addClass('d-none');
            top_buttons.children('[ceditor-element-social-media-btn="clear"]').addClass('d-none');
        }
    });

    $(document).on('click', '[ceditor-element-social-media-btn]', function () {
        var $this = $(this);
        var parent = $this.closest('[ceditor-element-item]');
        var action = $this.attr('ceditor-element-social-media-btn');

        if ((action == 'add' || action == 'replace') && parent != undefined && parent.length > 0) {
            var modal_html = '';
            var modal = parent.find('[ceditor-element-social-media-modal]');
            var modal_footer = $('body').find(ceditor_element_social_media_modal_block);

            if (modal_footer != undefined && modal_footer.length > 0) {
                modal_footer.remove();
            }

            modal_html = '<form method="POST" id="ceditor-element-social-media-modal">';
            modal_html += modal.html();
            modal_html += '</form>';

            // Insert modal
            $('body').append(modal_html);
            $(ceditor_element_social_media_modal_block + ' > .modal').modal('show').trigger('setdata');

            // Set selected item
            ceditor_selected_element_social_media = parent;
        }
    });

    $(document).on('click', '[ceditor-element-social-media-btn="clear"]', function () {
        var $this = $(this);
        var element = $this.closest('[ceditor-element-item]');
        var input = element.find('[content-editor-element-item-data]');

        input.val('');
        input.trigger('change');
    });

    $(document).on('submit', ceditor_element_social_media_modal_block, function (e) {
        e.preventDefault();

        var form_data = {};
        var element = $(this);
        var data = element.serializeArray();
        var modal = element.children('.modal');

        $.each(data, function (i, input) {
            var key = input.name;
            var value = input.value;
            form_data[key] = value;
        });

        if (ceditor_selected_element_social_media != undefined && ceditor_selected_element_social_media.length > 0) {
            var embed_code = '';
            var code_input = ceditor_selected_element_social_media.find('[content-editor-element-item-data]');
            var type_input = ceditor_selected_element_social_media.find('[content-editor-element-social-media-type]');
            var error_message = ceditor_selected_element_social_media.find('[content-editor-element-item-message="error"]');

            if (form_data.embed_code != undefined) {
                var valid = false;
                var embed_code_filtered = '';
                var type = type_input.attr('content-editor-element-social-media-type');

                if (type != undefined && type != '') {
                    var code = form_data.embed_code;
                    var $code = $(form_data.embed_code);

                    if (type == 'instagram') {
                        embed_code_filtered = form_data.embed_code;

                        $code.each(function () {
                            if ($(this).hasClass('instagram-media')) {
                                valid = true;
                            }
                        });
                    } else if (type == 'facebook') {
                        var src = $code.attr('src');
                        embed_code_filtered = form_data.embed_code;

                        if (src != undefined && (src.includes('facebook') | src.includes('fb'))) {
                            valid = true;
                        }
                    } else if (type == 'pinterest') {
                        var src = $code.attr('src');
                        embed_code_filtered = form_data.embed_code;

                        if (src != undefined && src.includes('pinterest')) {
                            valid = true;
                        }
                    } else if (type == 'tiktok') {
                        embed_code_filtered = form_data.embed_code;

                        $code.each(function () {
                            if ($(this).hasClass('tiktok-embed')) {
                                valid = true;
                            }
                        });
                    } else if (type == 'twitter') {
                        embed_code_filtered = form_data.embed_code;

                        $code.each(function () {
                            if ($(this).hasClass('twitter-tweet')) {
                                valid = true;
                            }
                        });
                    } else if (type == 'vk') {
                        embed_code_filtered = form_data.embed_code;

                        $code.each(function () {
                            var id = this.id;

                            if (id != undefined && id.includes('vk_post')) {
                                valid = true;
                            }
                        });
                    } else if (type == 'vimeo') {
                        $code.each(function () {
                            var src = $(this).attr('src');

                            if (src != undefined && src.includes('vimeo')) {
                                valid = true;
                                embed_code_filtered = $(this)[0].outerHTML;
                            }
                        });
                    } else if (type == 'youtube') {
                        $code.each(function () {
                            var src = $(this).attr('src');

                            if (src != undefined && (src.includes('youtube') || src.includes('youtu'))) {
                                valid = true;
                                embed_code_filtered = $(this)[0].outerHTML;
                            }
                        });
                    }
                }

                if (valid) {
                    embed_code = embed_code_filtered;
                }
            }

            if (embed_code != undefined && embed_code != '') {
                code_input.val(embed_code).trigger('change');
            } else {
                alert(error_message.val());
            }
        } else {
            console.log('Content editor error: Selected embed element not found.');
        }

        modal.modal('hide');
    });
}

ceditor_elements_init.tinymce = function () {
    $(document).on('content_editor_insert_element_tinymce', function () {
        contentEditorTinymceInit();
    });
}

ceditor_elements_init.video = function () {
    $(document).on('change', '[content-editor-element-item-data="video"]', function () {
        var $this = $(this);
        var url = $this.val();
        var parent = $this.closest('[ceditor-element-item]');
        var element = $this.closest('[ceditor-element]');
        var configs_block = element.find('[ceditor-element-configs]');
        var item_block = element.find('[ceditor-element-template-item]');
        var top_buttons = parent.find('.content-editor-element-top-actions');

        if (url != undefined && url != '') {
            var template_block = configs_block.find('[ceditor-element-template-block]').clone();
            template_block.find('source').attr('src', url);
            item_block.html(template_block.html());

            configs_block.addClass('d-none');
            top_buttons.children('.ceditor-element-video-btn-add').addClass('d-none');
            top_buttons.children('.ceditor-element-video-btn-replace').removeClass('d-none');
            top_buttons.children('.ceditor-element-video-btn-clear').removeClass('d-none');
        } else {
            item_block.empty();
            configs_block.removeClass('d-none');

            top_buttons.children('.ceditor-element-video-btn-add').removeClass('d-none');
            top_buttons.children('.ceditor-element-video-btn-replace').addClass('d-none');
            top_buttons.children('.ceditor-element-video-btn-clear').addClass('d-none');
        }
    });

    $(document).on('click', '.ceditor-element-video-btn-clear', function () {
        var $this = $(this);
        var element = $this.closest('[ceditor-element-item]');
        var input = element.find('[content-editor-element-item-data]');

        input.val('');
        input.trigger('change');
    });

    // Storage browser event
    storageBrowserEventCallback('onItemSelect', function (data) {
        if (data.action == 'content-editor-video') {
            var items = data.items;
            var button = data.button;
            var parent = button.closest('[ceditor-element-item]');

            if (items != undefined && items.length > 0) {
                var url = items[0];
                var input = parent.find('[content-editor-element-item-data]');

                input.val(url);
                input.trigger('change');
            }
        }
    });
}

// Insert event: Insert element
ceditor_event_insert_element.init = function (element_key, element) {
    var $input_data = element.find('[content-editor-element-item-data]');
    var $input_data_ex = element.find('[content-editor-element-item-data-ex]');

    if ($input_data != undefined && $input_data.length > 0) {
        $input_data.each(function () {
            var $this = $(this);
            var attr = $this.attr('content-editor-element-item-data-trigger');

            if (attr != undefined && attr == 'changed') {
                return true;
            } else {
                $this.attr('content-editor-element-item-data-trigger', 'changed');
                $this.trigger('change');
            }
        });
    }

    if ($input_data_ex != undefined && $input_data_ex.length > 0) {
        $input_data_ex.each(function () {
            var $this = $(this);
            var attr = $this.attr('content-editor-element-item-data-ex-trigger');

            if (attr != undefined && attr == 'changed') {
                return true;
            } else {
                $this.attr('content-editor-element-item-data-ex-trigger', 'changed');
                $this.trigger('change');
            }
        });
    }

    if (element_key == 'input') {
        element.find('[ceditor-color-picker]').colorpicker({ format: "hex" });
    }
}

// Render event: Element tempate
ceditor_event_render_template.init = function (element_key, element_type, data, clone, section_type, parent) {
    var is_multiple = false;
    var default_string = '';

    if (data.multiple != undefined && data.multiple === true) {
        is_multiple = true;
    }

    if (data.path != undefined && data.path != '') {
        clone.find('[storage-browser-show]').attr('storage-browser-path', data.path);
    }

    if (typeof data.default == 'string') {
        default_string = data.default;
    } else if (data.default != undefined) {
        default_string = JSON.stringify(data.default);
    }

    if (default_string != '') {
        var value_input = clone.find('[content-editor-element-item-data]');

        if (value_input.is('input')) {
            clone.find('[content-editor-element-item-data]').attr('value', default_string);
        }

        if (value_input.is('textarea')) {
            clone.find('[content-editor-element-item-data]').text(default_string);
        }
    }

    if (element_type == 'file' || element_type == 'image') {
        if (is_multiple) {
            var $button = clone.find('[storage-browser-show]');

            if ($button.attr('storage-browser-select-type') != undefined) {
                clone.find('[storage-browser-select-type]').attr('storage-browser-select-type', 'multi');
            } else {
                clone.find('[storage-browser-show]').attr('storage-browser-select-type', 'multi');
            }
        }
    }

    if (element_type == 'gallery') {
        clone.find('[stg-gallery--elements="data"]').attr('content-editor-element-item-data-ex', 'gallery');
        clone.find('[content-editor-element-gallery-items="data"]').attr('content-editor-element-item-data-ex', 'gallery');

        if (default_string != '') {
            clone.find('[stg-gallery--elements="data"]').attr('value', default_string);
            clone.find('[content-editor-element-gallery-items="data"]').attr('value', default_string);
        }
    }

    return clone;
}

// Render event: Social media element
ceditor_event_render_template.social_media = function (element_key, element_type, data, clone, section_type, parent) {
    if (data.embed != undefined && data.embed != '') {
        var embed_type = data.embed;
        var default_string = '';

        clone.find('[content-editor-element-social-media-type]').attr('content-editor-element-social-media-type', embed_type);
        clone.find('[ceditor-element-template-item]').attr('ceditor-element-template-item', embed_type);

        if (data.icon != undefined && data.icon != '') {
            clone.find('.content-editor-social-media-icon').html(data.icon);
        }

        if (data.title != undefined && data.title != '') {
            clone.find('.content-editor-social-media-title').html(data.title);
        }
    }

    return clone;
}

function ceditor_social_media_vk_api() {
    var vk_script = $('#vk-openapi-script');
    var vk_script_code = document.createElement('script');
    vk_script_code.id = "vk-openapi-script";
    vk_script_code.type = "text/javascript";
    vk_script_code.src = "https://vk.com/js/api/openapi.js?169";

    if (vk_script != undefined && vk_script.length > 0) {
        return true;
    } else {
        $('body').append(vk_script_code);
    }

    return false;
}